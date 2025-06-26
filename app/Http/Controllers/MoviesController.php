<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\critiques;
use App\Models\movies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoviesController extends Controller
{
    public function index() {
        $movies = movies::orderBy('avg_note', 'desc')->get();
        return view('home', [
            'movies' => $movies
            
        ]);
        
    }
    public function admin() {
        $movies = movies::orderBy('avg_note', 'desc')->get();
        return view('admin', [
            'movies' => $movies
            
        ]);
        
    }
    public function delete(string $id)
    {
        $movie = movies::findOrFail($id);
        $movie->critiques()->delete();
        if($movie->delete()) {
            return redirect()->route('movies')->with('success', 'Le film a bien été supprimé');
        }
    }

    public function autocomplete(Request $request)
    {
        
        $query = $request->get('query');

        if (!$query) {
            return response()->json([]);
        }

        $apiKey =config('services.tmdb.key');

        $response = Http::get('https://api.themoviedb.org/3/search/movie', [
            'api_key' => $apiKey,
            'query' => $query,
            'language' => 'fr-FR',
            'page' => 1,
            'include_adult' => false,
        ]);

        $results = $response->json()['results'] ?? [];

        $movies = array_map(function ($movie) {
            return [
                'id' => $movie['id'],
                'title' => $movie['title'],
                'release_date' => $movie['release_date'] ?? '',
            ];
        }, $results);

        return response()->json($movies);
    }

    public function storeFromSearch(Request $request)
{
    $query = $request->input('query');
    $id = $request->input('id');
    $tmdbApiKey = config('services.tmdb.key');

    $ifexistcat= categories::all();

    if(empty($ifexistcat)){
    // 1. Synchroniser les catégories depuis TMDb
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
    // 2. Si aucun ID, on cherche par nom
    if (empty($id) && $query) {
        $searchResponse = Http::get("https://api.themoviedb.org/3/search/movie", [
            'query'    => $query,
            'language' => 'fr-FR',
            'api_key'  => $tmdbApiKey,
        ]);

        $firstResult = $searchResponse->json()['results'][0] ?? null;

        if (!$firstResult) {
            return redirect()->route('movies')->with('error', 'Film non trouvé.');
        }

        $id = $firstResult['id']; // Met à jour l’ID trouvé
    }
    $ifmovieid = movies::where('tmdb_id',$id)->first();
    // 3. Récupérer le film par son ID
    if(empty($ifmovieid)){
    $response = Http::get("https://api.themoviedb.org/3/movie/{$id}", [
        'language' => 'fr-FR',
        'api_key' => $tmdbApiKey,
    ]);
    
    if ($response->failed()) {
        return redirect()->route('movies')->with('error', 'Erreur lors de la récupération du film.');
    }

    $tmdbMovie = $response->json();

    // 4. Vérification de doublon
    $existing = movies::where('tmdb_id', $tmdbMovie['id'])->first();
    if ($existing) {
        return to_route('movies.show', ['slug' => Str::slug($tmdbMovie['title'] ?? 'titre'), 'tmdb_id' => $tmdbMovie['id']]);
        
    }

    // 5. Récupérer le casting
    $castResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/credits", [
        'api_key' => $tmdbApiKey,
        'language' => 'fr-FR',
    ]);
    $cast = collect($castResponse->json()['cast'] ?? [])->take(5)->pluck('name')->join(', ');

    // 6. Récupérer la bande-annonce
    $videoResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/videos", [
        'api_key' => $tmdbApiKey,
        'language' => 'fr-FR',
    ]);
    $trailer = collect($videoResponse->json()['results'] ?? [])->firstWhere('type', 'Trailer');
    $trailerUrl = $trailer ? 'https://www.youtube.com/watch?v=' . $trailer['key'] : null;

    // 7. Catégorie principale du film (ou catégorie par défaut si absente)
    $firstGenreId = null;

    if (!empty($tmdbMovie['genres'])) {
        $genreId = $tmdbMovie['genres'][0]['id'];
        $category = categories::where('id_cat', $genreId)->first();
    
        if (!$category) {
            // Créer la catégorie manquante
            $category = categories::create([
                'id_cat' => $genreId,
                'title_cat' => $tmdbMovie['genres'][0]['name'] ?? 'Inconnu',
            ]);
        }
    
        $firstGenreId = $category->id_cat;
    } else {
        // Catégorie de secours
        $defaultCat = categories::firstOrCreate(
            ['id_cat' => 9999],
            ['title_cat' => 'Non classé']
        );
        $firstGenreId = $defaultCat->id_cat;
    }
    

    // 8. Enregistrement du film
    movies::create([
        'tmdb_id'     => $tmdbMovie['id'],
        'slug'        => Str::slug($tmdbMovie['title'] ?? 'titre'),
        'movie_title' => $tmdbMovie['title'] ?? 'Titre inconnu',
        'synopsis'    => $tmdbMovie['overview'] ?? '',
        'year'        => isset($tmdbMovie['release_date']) ? substr($tmdbMovie['release_date'], 0, 4) : null,
        'casting'     => $cast,
        'image'       => isset($tmdbMovie['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $tmdbMovie['poster_path'] : null,
        'trailler'    => $trailerUrl,
        'avg_note'    => $tmdbMovie['vote_average'] ?? 0,
        'id_cat'      => $firstGenreId,
    ]);
    return to_route('movies.show', ['slug' => Str::slug($tmdbMovie['title'] ?? 'titre'), 'tmdb_id' => $tmdbMovie['id']]);

    }
    return to_route('movies.show', ['slug' => Str::slug($ifmovieid->movie_title ?? 'titre'), 'tmdb_id' => $ifmovieid->tmdb_id]);

}

public function show(string $slug, string $tmdb_id)
{
    $critiques = critiques::with('likes')->get();
    $movie = Movies::where('tmdb_id', $tmdb_id)->firstOrFail(); // ou ce que tu utilises

    if ($movie->slug !== $slug) {
        return to_route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]);
    }

    return view('show', [
        'movie' => $movie,
        'categories' => Categories::all(),
        'critiques' => $critiques
    ]);
}
}