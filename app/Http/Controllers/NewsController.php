<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\TmdbService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $news = News::all();
        $newsClass= News::class;
        return view('news', ['news' => $news,'newsClass'=>$newsClass]);
    }

    public function create(TmdbService $tmdbService)
    {
        // Vérifie l'autorisation de créer une actualité
        $this->authorize('create', News::class);

        // Supprimer toutes les actualités existantes
        News::truncate(); // Plus efficace que foreach + delete

        
        $films = $tmdbService->getNews() ;

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
