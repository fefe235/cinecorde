<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\movies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoviesController extends Controller
{
    public function index() {
        $movies = movies::all();
        return view('home', [
            'movies' => $movies,
            'categories' => categories::all()
            
        ]);
        
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
    $id = $request->input('id');

    if (empty($id)) {
        return redirect()->route('movies')->with('error', 'Veuillez sélectionner un film depuis les suggestions.');
    }

    $tmdbApiKey = config('services.tmdb.key');

    // 1. Récupérer la liste des catégories TMDb
    $genreResponse = Http::get('https://api.themoviedb.org/3/genre/movie/list', [
        'api_key' => $tmdbApiKey,
        'language' => 'fr-FR',
    ]);

    if ($genreResponse->failed()) {
        return redirect()->route('movies')->with('error', 'Erreur lors de la récupération des catégories.');
    }

    $tmdbGenres = $genreResponse->json()['genres'] ?? [];

    // Synchroniser les catégories TMDb dans ta table 'categories'
    foreach ($tmdbGenres as $genre) {
        categories::updateOrCreate(
            ['id_cat' => $genre['id']], // id_cat = id TMDb
            ['title_cat' => $genre['name']] // nom_cat = nom du genre
        );
    }

    // 2. Récupérer le film via son ID
    $response = Http::get("https://api.themoviedb.org/3/movie/{$id}", [
        'language' => 'fr-FR',
        'api_key' => $tmdbApiKey,
    ]);

    if ($response->failed()) {
        return redirect()->route('movies')->with('error', 'Erreur lors de la récupération du film.');
    }

    $tmdbMovie = $response->json();

    if (empty($tmdbMovie) || isset($tmdbMovie['status_code'])) {
        return redirect()->route('movies')->with('error', 'Film non trouvé.');
    }

    // Vérifie si le film existe déjà
    $existing = movies::where('tmdb_id', $tmdbMovie['id'])->first();
    if ($existing) {
        return redirect()->route('movies')->with('message', 'Film déjà enregistré.');
    }

    // Récupérer casting
    $castResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/credits", [
        'api_key' => $tmdbApiKey,
        'language' => 'fr-FR',
    ]);
    $cast = collect($castResponse->json()['cast'] ?? [])->take(5)->pluck('name')->join(', ');

    // Récupérer bande-annonce
    $videoResponse = Http::get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/videos", [
        'api_key' => $tmdbApiKey,
        'language' => 'fr-FR',
    ]);
    $trailer = collect($videoResponse->json()['results'] ?? [])
        ->firstWhere('type', 'Trailer');
    $trailerUrl = $trailer ? 'https://www.youtube.com/watch?v=' . $trailer['key'] : null;

    // Associer la première catégorie TMDb s'il y en a
    $firstGenreId = $tmdbMovie['genres'][0]['id'] ?? null;

    // Enregistrer le film en base avec la bonne catégorie
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
        'id_cat'      => $firstGenreId, // correspond à id_cat dans ta table categories
    ]);

    return redirect()->route('movies')->with('message', 'Film enregistré avec succès.');
}
public function show(string $slug, string $tmdb_id)
{
    $movie = Movies::where('tmdb_id', $tmdb_id)->firstOrFail();

    if ($movie->slug !== $slug) {
        return to_route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]);
    }

    return view('show', [
        'movie' => $movie,
        'categories' => Categories::all()
    ]);
}
}