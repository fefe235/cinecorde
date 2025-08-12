<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function toggle($movieId)
    {
        $user = Auth::user();

        // Si déjà favori → on retire
        if ($user->favoriteMovies()->where('movie_id', $movieId)->exists()) {
            $user->favoriteMovies()->detach($movieId);
            return back()->with('success', 'Film retiré des favoris.');
        }

        // Sinon → on ajoute
        $user->favoriteMovies()->attach($movieId);
        return back()->with('success', 'Film ajouté aux favoris.');
    }
}
