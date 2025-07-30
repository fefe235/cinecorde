<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\critiques;
use App\Models\Like;
use App\Models\movies;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MoviesController extends Controller
{
    public function index()
    {
        //classer par note
        $movies = movies::orderBy('avg_note', 'desc')->paginate(25);
        return view('home', [
            'movies' => $movies

        ]);

    }
    public function admin()
    {
        $movies = movies::orderBy('avg_note', 'desc')->get();
        return view('admin', [
            'movies' => $movies

        ]);

    }
    public function delete(string $id)
    {
        $movie = movies::findOrFail($id);
        $movie->critiques()->delete();
        if ($movie->delete()) {
            return redirect()->route('movies')->with('success', 'Le film a bien été supprimé');
        }
    }

    public function autocomplete(Request $request)
    {
        //stoctk ce que a saisi l'utilisateur
        $query = $request->get('query');

        if (!$query) {
            return response()->json([]);
        }

        $apiKey = config('services.tmdb.key');

        //execute une requette http pour faire une recherche a partir de ce que l'utilisateur a saisi
        $response = Http::get('https://api.themoviedb.org/3/search/movie', [
            'api_key' => $apiKey,
            'query' => $query,
            'language' => 'fr-FR',
            'page' => 1,
            'include_adult' => false,
        ]);
        //stock les résultat dans un fichier json
        $results = $response->json()['results'] ?? [];

        $movies = array_map(function ($movie) {
            return [
                'id' => $movie['id'],
                'title' => $movie['title'],
                'release_date' => $movie['release_date'] ?? '',
            ];
        }, $results);
        //envoie le fichier json qui contient les résultats(de la barre de recherche) pour les affichés coté js
        return response()->json($movies);
    }

    public function storeFromSearch(Request $request)
    {
        $query = $request->input('query');
        $id = $request->input('id');
        $tmdbApiKey = config('services.tmdb.key');

        $ifexistcat = categories::all();
        //verifie si les categorie sont dans la base de donné pour stocker ou non les données
        if (empty($ifexistcat)) {
            //  Synchroniser les catégories depuis TMDb
            $genreResponse = Http::get('https://api.themoviedb.org/3/genre/movie/list', [
                'api_key' => $tmdbApiKey,
                'language' => 'fr-FR',
            ]);

            if ($genreResponse->failed()) {
                return redirect()->route('movies')->with('error', 'Erreur lors de la récupération des catégories.');
            }

            $tmdbGenres = $genreResponse->json()['genres'] ?? [];

            foreach ($tmdbGenres as $genre) {
                categories::updateOrCreate(
                    ['id_cat' => $genre['id']],
                    ['title_cat' => $genre['name']]
                );
            }
        }
        //  Si aucun ID, on cherche par nom
        if (empty($id) && $query) {
            $searchResponse = Http::get("https://api.themoviedb.org/3/search/movie", [
                'query' => $query,
                'language' => 'fr-FR',
                'api_key' => $tmdbApiKey,
            ]);


            $firstResult = $searchResponse->json()['results'][0] ?? null;
            if (empty($firstResult)) {
                return redirect()->route('movies')->with('error', 'Film non trouvé.');
            }

            $id = $firstResult['id']; // Met à jour l’ID trouvé(le film a maintenant un id)
        }
        $ifmovieid = movies::where('tmdb_id', $id)->first();
        // Récupérer le film par son ID
        if (empty($ifmovieid)) {
            $response = Http::get("https://api.themoviedb.org/3/movie/{$id}", [
                'language' => 'fr-FR',
                'api_key' => $tmdbApiKey,
            ]);

            if ($response->failed()) {
                return redirect()->route('movies')->with('error', 'Erreur lors de la récupération du film.');
            }

            $tmdbMovie = $response->json();
            //  Vérification de doublon
            $existing = movies::where('tmdb_id', $tmdbMovie['id'])->first();
            if (isset($existing)) {
                return to_route('movies.show', ['slug' => Str::slug($tmdbMovie['title'] ?? 'titre'), 'tmdb_id' => $tmdbMovie['id']]);

            }

            //  Récupérer le casting
            $castResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/credits", [
                'api_key' => $tmdbApiKey,
                'language' => 'fr-FR',
            ]);
            $cast = collect($castResponse->json()['cast'] ?? [])->take(5)->pluck('name')->join(', ');

            // Récupérer la bande-annonce
            $videoResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/videos", [
                'api_key' => $tmdbApiKey,
                'language' => 'fr-FR',
            ]);
            $trailer = collect($videoResponse->json()['results'] ?? [])->firstWhere('type', 'Trailer');
            $trailerUrl = $trailer ? 'https://www.youtube.com/watch?v=' . $trailer['key'] : null;


            //  Catégorie principale du film (ou catégorie par défaut si absente)
            $firstGenreId = null;

            $genreResponse = Http::get('https://api.themoviedb.org/3/genre/movie/list', [
                'api_key' => $tmdbApiKey,
                'language' => 'fr-FR',
            ]);

            if ($genreResponse->failed()) {
                return redirect()->route('movies')->with('error', 'Erreur lors de la récupération des catégories.');
            }

            $tmdbGenres = $genreResponse->json()['genres'] ?? [];

            foreach ($tmdbGenres as $genre) {
                categories::updateOrCreate(
                    ['id_cat' => $genre['id']],
                    ['title_cat' => $genre['name']]
                );
            }
            $genreIds = [];
            if (!empty($tmdbMovie['genres'])) {
                foreach ($tmdbMovie['genres'] as $genre) {
                    $category = categories::firstOrCreate(
                        ['id_cat' => $genre['id']],
                        ['title_cat' => $genre['name'] ?? 'Inconnu']
                    );
                    $genreIds[] = $category->id_cat; // ID interne (auto-increment) de la table categories
                }
            }
            // Enregistrer le film
            // Construire l'URL de l'image (poster)
            $storeImageUrl = 'https://image.tmdb.org/t/p/w500' . $tmdbMovie['poster_path'];

            // Téléchargement de l'image
            $response = Http::get($storeImageUrl);

            // Vérifier que l’image a bien été récupérée
            if ($response->successful()) {
                // Nettoyer le titre du film pour en faire un nom de fichier correct
                $safeFilename = Str::slug($tmdbMovie['title'] ?? 'image') . '.jpg';

                // Stocker l’image dans le dossier "storage/app/public/posters"
                $success = Storage::disk('public')->put("posters/{$safeFilename}", $response->body());

                // Chemin web vers l’image
                $posterPath = $success ? "storage/posters/{$safeFilename}" : null;
            } else {
                $posterPath = null;
            }

            $movie = movies::create([
                'tmdb_id' => $tmdbMovie['id'],
                'slug' => Str::slug($tmdbMovie['title'] ?? "titre"),
                'movie_title' => $tmdbMovie['title'] ?? 'Titre inconnu',
                'synopsis' => $tmdbMovie['overview'] ?? '',
                'year' => isset($tmdbMovie['release_date']) ? substr($tmdbMovie['release_date'], 0, 4) : null,
                'casting' => $cast,
                'image' => $posterPath ?? null,
                'trailler' => $trailerUrl,
                'avg_note' => $tmdbMovie['vote_average'] ?? 0,
            ]);

            // Associer les catégories au film
            $movie->categories()->sync($genreIds); // ou ->attach($genreIds) si création initiale

            // 8. Enregistrement du film

            return to_route('movies.show', ['slug' => Str::slug($tmdbMovie['title'] ?? 'titre'), 'tmdb_id' => $tmdbMovie['id']]);

        }
        return to_route('movies.show', ['slug' => Str::slug($ifmovieid->movie_title ?? 'titre'), 'tmdb_id' => $ifmovieid->tmdb_id]);

    }

    public function show(string $slug, string $tmdb_id)
    {
        $movie = Movies::where('tmdb_id', $tmdb_id)->firstOrFail(); // ou ce que tu utilises

        // Récupérer toutes les critiques du film avec les relations
        $critiques = critiques::with(['user', 'likes'])
            ->where('id_movie', $movie->id_movie)
            ->get();

        $userLikedCritiques = [];

        if (Auth::check()) {
            $userId = Auth::user()->user_id;

            // Récupérer tous les likes de l'utilisateur pour les critiques de ce film
            $liked = Like::where('user_id', $userId)
                ->whereIn('critique_id', $critiques->pluck('id_critique'))
                ->pluck('critique_id')
                ->toArray();

            $userLikedCritiques = array_flip($liked); // Pour un accès rapide via isset()

        }
        if ($movie->slug !== $slug) {
            return to_route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]);
        }

        return view('show', [
            'movie' => $movie,
            'categories' => Categories::all(),
            'critiques' => $critiques,
            'userLikedCritiques' => $userLikedCritiques
        ]);
    }
}