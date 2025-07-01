<?php

namespace App\Http\Controllers;

use App\Models\news;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = news::all();
        return view('news', ['news'=>$news]);
    }
    public function create(){
        $news = news::all();
        foreach($news as $new){
            $new->delete();
        }
        $response = Http::get('https://api.themoviedb.org/3/trending/movie/week', [
            'api_key' => config('services.tmdb.key'),
            'language' => 'fr-FR'
        ]);

        $films = $response->json()['results'];
        
        foreach ($films as $film){

            news::create([
                "title_news" => $film['title'],
                "news"      => $film['overview'],
                "image"     => $film['poster_path'],
                "release_date"=>$film['release_date']
            ]);

        }
        return redirect()->route("actualites");
    }
}
