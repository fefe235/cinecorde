<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CritiquesController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\NewsController;
use App\Http\Middleware\Auth;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

Route::get('/autocomplete-movies', [MoviesController::class, 'autocomplete'])->name('movies.auto');
Route::post('/movies/search/', [MoviesController::class, 'storeFromSearch'])->name('movies.search');
Route::get('/',[MoviesController::class,'index'])->name('movies');
Route::get('/movies/{slug}/{tmdb_id}/', [MoviesController::class, 'show'])
        ->name('movies.show')
        ->where(['slug' => '[a-z0-9\-]+','tmdb_id' => '[0-9]+']);
Route::post('/critique/new', [CritiquesController::class, 'create'])->middleware('auth')->name('critique.create');
Route::get('/critique/{id}/edit', [CritiquesController::class, 'edit'])->middleware('auth')->name('critique.edit');
Route::post('/critique/{id}/edit', [CritiquesController::class, 'update'])->middleware('auth')->name('critique.update');
Route::post('/critique/{id}/delete', [CritiquesController::class, 'delete'])->middleware('auth')->name('critique.delete');
Route::get('/filmActualites',[NewsController::class,'index'])->name('actualites');
Route::get('/filmCategories',[CategoriesController::class,'index'])->name('categories');
Route::get('/inscription', [AuthController::class, 'register'])->name('auth.register');
Route::post('/inscription', [AuthController::class, 'registercreate'])->name('auth.registercreate');
Route::get('/connexion', [AuthController::class, 'login'])->name('auth.login');
Route::post('/connexion', [AuthController::class, 'doLogin'])->name('auth.doLogin');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('auth.logout');

