<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $news = News::all();
        $news2= News::class;
        return view('news', ['news' => $news,'news2'=>$news2]);
    }

    public function create()
    {
        // Vérifie l'autorisation de créer une actualité
        $this->authorize('create', News::class);

        // Supprimer toutes les actualités existantes
        News::truncate(); // Plus efficace que foreach + delete

        // Récupérer les films tendances depuis l'API
        $response = Http::get('https://api.themoviedb.org/3/trending/movie/week', [
            'api_key' => config('services.tmdb.key'),
            'language' => 'fr-FR'
        ]);

        $films = $response->json()['results'] ?? [];

        // Ajouter les nouvelles actualités
        foreach ($films as $film) {
            News::create([
                'title_news' => $film['title'],
                'news' => $film['overview'],
                'image' => $film['poster_path'],
                'release_date' => $film['release_date'] ?? null
            ]);
        }
        
        return redirect()->route('actualites');
    }
}
