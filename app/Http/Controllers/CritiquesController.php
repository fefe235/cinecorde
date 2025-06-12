<?php

namespace App\Http\Controllers;

use App\Models\critiques;
use App\Models\movies;
use Illuminate\Http\Request;

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
}