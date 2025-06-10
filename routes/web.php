<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/autocomplete-movies', [MoviesController::class, 'autocomplete']);
Route::post('/movies/search', [MoviesController::class, 'storeFromSearch'])->name('movies.search');
Route::get('/',[MoviesController::class,'index'])->name('movies');
Route::get('/movies/{slug}/{tmdb_id}/', [MoviesController::class, 'show'])
        ->name('movies.show')
        ->where(['slug' => '[a-z0-9\-]+','tmdb_id' => '[0-9]+']);
Route::get('/filmActualites',[NewsController::class,'index'])->name('actualites');
Route::get('/filmCategories',[CategoriesController::class,'index'])->name('categories');
