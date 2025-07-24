<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\movies;


class CategoriesController extends Controller
{
    public function index() {
        $categories = Categories::with('movies')->get();
        $movies = movies::all(); // eager load
        return view('categories',compact('categories','movies'));
    }
}
