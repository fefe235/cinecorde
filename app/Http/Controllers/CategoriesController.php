<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\movies;
use Illuminate\Http\Request;

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
