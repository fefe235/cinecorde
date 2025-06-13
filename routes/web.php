<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CritiquesController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\NewsController;
use App\Models\critiques;
use Illuminate\Support\Facades\Route;

Route::get('/autocomplete-movies', [MoviesController::class, 'autocomplete']);
Route::post('/movies/search/', [MoviesController::class, 'storeFromSearch'])->name('movies.search');
Route::get('/',[MoviesController::class,'index'])->name('movies');
Route::get('/movies/{slug}/{tmdb_id}/', [MoviesController::class, 'show'])
        ->name('movies.show')
        ->where(['slug' => '[a-z0-9\-]+','tmdb_id' => '[0-9]+']);
Route::post('/critique/new', [CritiquesController::class, 'create'])->name('critique.create');
Route::get('/article/{id}/edit', [CritiquesController::class, 'edit'])->name('critique.edit');
Route::post('/article/{id}/edit', [CritiquesController::class, 'update'])->name('critique.update');
Route::post('/article/{id}/delete', [CritiquesController::class, 'delete'])->name('critique.delete');
Route::get('/filmActualites',[NewsController::class,'index'])->name('actualites');
Route::get('/filmCategories',[CategoriesController::class,'index'])->name('categories');
