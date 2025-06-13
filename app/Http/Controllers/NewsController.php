<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $response = Http::get('https://api.themoviedb.org/3/trending/movie/week', [
            'api_key' => config('services.tmdb.key'),
            'language' => 'fr-FR'
        ]);

        $films = $response->json()['results'];

        return view('news', compact('films'));
    }
}
