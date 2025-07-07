<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\movies;


class CategoriesController extends Controller
{
    public function index() {
        $categories = categories::all();
        $movies = movies::all();
        return view('categories',[
            'categories' => $categories,
            'movies' => $movies
        ]);
        
    }
}
