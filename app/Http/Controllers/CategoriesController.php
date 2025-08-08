<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Movies;


class CategoriesController extends Controller
{
    public function index() {
        $selectedCategoryId = request()->get('id') ?? Categories::pluck('id_cat')->random();
    $categoriesWithMovies = Categories::with('movies')->get();

    $selectedCategory = null;
    $filteredMovies = collect(); // vide par défaut
    if ($selectedCategoryId) {
        $selectedCategory = Categories::with('movies')->find($selectedCategoryId);
        if ($selectedCategory) {
            $filteredMovies = $selectedCategory->movies()->paginate(25);
        }
    }

    return view('categories', compact('categoriesWithMovies', 'filteredMovies', 'selectedCategoryId'));
    }
}
