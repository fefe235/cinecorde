<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Movies;
use App\Models\Categories;

class TmdbService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
    }

    public function searchMovie(string $query): array
    {
        $response = Http::get("https://api.themoviedb.org/3/search/movie", [
            'query' => $query,
            'language' => 'fr-FR',
            'api_key' => $this->apiKey,
        ]);

        return $response->json()['results'][0] ?? [];
    }
    public function searchMovies(string $query): array
    {
        $response = Http::get("https://api.themoviedb.org/3/search/movie", [
            'query' => $query,
            'language' => 'fr-FR',
            'api_key' => $this->apiKey,
        ]);

        return $response->json()['results'] ?? [];
    }

    public function getMovieById(int $id): ?array
    {
        $response = Http::get("https://api.themoviedb.org/3/movie/{$id}", [
            'language' => 'fr-FR',
            'api_key' => $this->apiKey,
        ]);

        return $response->failed() ? null : $response->json();
    }

    public function getCast(int $id): string
    {
        $castResponse = Http::get("https://api.themoviedb.org/3/movie/{$id}/credits", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
        ]);

        return collect($castResponse->json()['cast'] ?? [])->take(5)->pluck('name')->join(', ');
    }

    public function getTrailer(int $id): ?string
    {
        $videoResponse = Http::get("https://api.themoviedb.org/3/movie/{$id}/videos", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
        ]);

        $trailer = collect($videoResponse->json()['results'] ?? [])->firstWhere('type', 'Trailer');
        return $trailer ? 'https://www.youtube.com/watch?v=' . $trailer['key'] : null;
    }
    // public function getGenre(int $id): ?string
    // {
    //     $genreResponse = Http::get('https://api.themoviedb.org/3/genre/movie/list', [
    //         'api_key' => $this->apiKey,
    //         'language' => 'fr-FR',
    //     ]);

    //     if ($genreResponse->failed()) {
    //         return redirect()->route('movies')->with('error', 'Erreur lors de la récupération des catégories.');
    //     }
    //     return $genreResponse;
    // }

    public function savePoster(string $title, ?string $path): ?string
    {
        if (!$path) return null;

        $storeImageUrl = 'https://image.tmdb.org/t/p/w500' . $path;
        $response = Http::get($storeImageUrl);

        if (!$response->successful()) return null;

        $safeFilename = Str::slug($title ?? 'image') . '.jpg';
        Storage::disk('public')->put("posters/{$safeFilename}", $response->body());

        return "storage/posters/{$safeFilename}";
    }

    public function syncCategories(array $genres): array
    {
        $ids = [];
        foreach ($genres as $genre) {
            $category = Categories::firstOrCreate(
                ['title_cat' => $genre['name']],
                ['id_cat' => $genre['id']]
            );
            $ids[] = $category->id_cat;
        }
        return $ids;
    }
}
