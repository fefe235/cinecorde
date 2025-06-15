<?php

namespace App\Http\Controllers;

use App\Models\critiques;
use App\Models\movies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CritiquesController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'rate' => 'required',
            'id_movie'=>'required',
            'id_user'=>'required',
            'critique' => 'required|min:10'
        ]);

        critiques::create([
            'note' => $request->input('rate'),
            'id_movie'=>$request->input('id_movie'),
            'id_user'=>$request->input('id_user'),
            'critique' =>  $request->input('critique'),
            'nbr_like' =>  0,
        ]);
        $movie = movies::where('id_movie', $request->input('id_movie'))->firstOrFail();
        $movie->avg_note = Critiques::where('id_movie', $movie->id_movie)->avg('note');
        $movie->save();
        return view('show', [
            'critiques' => critiques::all(),
            'movie'=> $movie
        ]);
}
public function edit(string $id)
{
    return view('edit', [
        'critique' => critiques::findOrFail($id)
    ]);
}

public function update(string $id, Request $request)
{
    $critique = critiques::findOrFail($id);

    $request->validate([
        'rate' => 'required',
        'critique' => 'required|min:10'
    ]);

    $critique->note = $request->input('rate');
    $critique->critique = $request->input('critique');
    $critique->save();
    $movie = movies::where('id_movie', $critique->id_movie)->firstOrFail();
    return to_route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]);
}

public function delete(string $id)
{
    $critique = critiques::findOrFail($id);
    $movie = movies::where('id_movie', $critique->id_movie)->firstOrFail();
    
    if($critique->delete()) {
        return to_route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]);
    }
}
}