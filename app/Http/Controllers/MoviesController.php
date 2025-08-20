<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Critiques;
use App\Models\Like;
use App\Models\movies;
use App\Services\TmdbService;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MoviesController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {   
        
        //classer par note
        $movies = Movies::orderBy('avg_note', 'desc')->paginate(25);
        return view('home', [
            'movies' => $movies,
        ]);

    }
    public function delete(string $id)
    {
        $movie = Movies::findOrFail($id);
        $this->authorize('delete', $movie);
        $movie->critiques()->delete();
        if ($movie->delete()) {
            return redirect()->route('movies')->with('success', 'Le film a bien été supprimé');
        }
    }

    public function autocomplete(Request $request,TmdbService $tmdbService)
    {
        //stoctk ce que a saisi l'utilisateur
        $query = $request->get('query');

        if (!$query) {
            return response()->json([]);
        }


        $results= $tmdbService->searchMovies($query);

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

    public function storeFromSearch(Request $request, TmdbService $tmdbService)
    {
        $query = $request->input('query');
        $id = $request->input('id');
        


        //  Si aucun ID, on cherche par nom
        if (empty($id) && $query) {


            $firstResult = $tmdbService->searchMovie($query) ?? null;
            if (empty($firstResult)|| empty($firstResult['poster_path'])) {
                return redirect()->route('movies')->with('error', 'Film non trouvé.');
            }

            $id = $firstResult['id']; // Met à jour l’ID trouvé(le film a maintenant un id)
        }
        $ifmovieid = Movies::where('tmdb_id', $id)->first();
        // Récupérer le film par son ID
        if (empty($ifmovieid)) {
      
            $tmdbMovie = $tmdbService->getMovieById($id);
            //  Vérification de doublon
            $existing = Movies::where('tmdb_id', $tmdbMovie['id'])->first();
            if (isset($existing)) {
                return to_route('movies.show', ['slug' => Str::slug($tmdbMovie['title'] ?? 'titre'), 'tmdb_id' => $tmdbMovie['id']]);

            }

            //  Récupérer le casting
           
            $cast = $tmdbService->getCast($id);

            // Récupérer la bande-annonce
            $trailerUrl = $tmdbService->getTrailer($id);


            //  Catégorie principale du film (ou catégorie par défaut si absente)

            $genreIds = [];
            if (!empty($tmdbMovie['genres'])) {
                foreach ($tmdbMovie['genres'] as $genre) {
                    $category = Categories::firstOrCreate(
                        ['title_cat' => $genre['name']], // identifiant stable
                        ['id_cat' => $genre['id']]       // facultatif, juste pour info
                    );
                    $genreIds[] = $category->id_cat;
                }
            }
            // Enregistrer le film
            // Construire l'URL de l'image (poster)
            $posterPath = $tmdbService->savePoster($tmdbMovie['title'],$tmdbMovie['poster_path']);
            
            $movie = Movies::create([
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
            $genreIds = array_unique($genreIds);
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
        $critiques = Critiques::with(['user', 'likes'])
            ->where('id_movie', $movie->id_movie)->orderBy('nbr_like','desc')
            ->paginate(50);

        $userLikedCritiques = [];
        $userId = null;
        
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
            'userLikedCritiques' => $userLikedCritiques,
            'userId' => $userId,
        ]);
    }
}