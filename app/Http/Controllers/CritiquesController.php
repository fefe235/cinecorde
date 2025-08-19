<?php

namespace App\Http\Controllers;

use App\Models\Critiques;
use App\Models\Movies;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;

class CritiquesController extends Controller
{
    use AuthorizesRequests;
    public function dislike($id_critique)
    {
        $userId = Auth::id();

        // Trouver le like existant pour cette critique et cet utilisateur
        $like = Like::where('critique_id', $id_critique)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();

            // décrémenter le compteur si tu as un champ `nbr_like`
            $critique = Critiques::find($id_critique);
            if ($critique && $critique->nbr_like > 0) {
                $critique->decrement('nbr_like');
                User::where('user_id', Auth::user()->user_id)->decrement('nbr_like_total');
            }
        }
        $this->updateAllRanks();
        return back()->with('success', 'Like supprimé');
    }

    public function like($id_critique) // $id_critique vient de la route
    {
        $userId = auth()->user()->user_id;

        // Vérifie si ce user a déjà liké cette critique
        $alreadyLiked = Like::where('user_id', $userId)
            ->where('critique_id', $id_critique)
            ->exists();

        if (!$alreadyLiked) {
            Like::create([
                'user_id' => $userId,
                'critique_id' => $id_critique,
            ]);

            // Incrémente le compteur de like dans la critique
            Critiques::where('id_critique', $id_critique )->increment('nbr_like');
            User::where('user_id', $id_critique)->increment('nbr_like_total');
        }
        $this->updateAllRanks();
        return back();
    }
    public function updateAllRanks()
    {
        $users = User::orderByDesc('nbr_like_total')->get();
        $rank = 1;
    
        foreach ($users as $user) {
            $user->update(['rank' => $rank]);
            $rank++;
        }
    }
    
    

    public function create(Request $request)
    {
        //verifier la critique
        $request->validate([
            'rate' => 'required',
            'id_movie' => 'required',
            'id_user' => 'required',
            'critique' => 'required|min:10|max:1000'
        ]);
        $userId = auth()->id();
        $movieId = $request->input('id_movie');
        //verifier si l'utilisateur a deja critiqué
        $existingCritique = Critiques::where('id_user', $userId)
            ->where('id_movie', $movieId)
            ->first();

        if ($existingCritique) {
            return back()->with('error', 'Vous avez déjà posté une critique pour ce film.');
        }
        //mets la critique dans la base de données
        Critiques::create([
            'note' => $request->input('rate'),
            'id_movie' => $request->input('id_movie'),
            'id_user' => $request->input('id_user'),
            'critique' => $request->input('critique'),
            'nbr_like' => 0,
        ]);
        //mets a jour la note moyenne du film
        $movie = Movies::where('id_movie', $request->input('id_movie'))->firstOrFail();
        $movie->avg_note = Critiques::where('id_movie', $movie->id_movie)->avg('note');
        $movie->save();
        return back();
    }
    public function edit(string $id)
    {
        return view('edit', [
            'critique' => Critiques::findOrFail($id)
        ]);
    }

    public function update(string $id, Request $request)
    {
        //chercher la critique dans la base
        $critique = Critiques::findOrFail($id);
        $this->authorize('update',$critique);

        //valider la critique saisie
        $request->validate([
            'rate' => 'required',
            'critique' => 'required|min:10|max:1000'
        ]);
        //metre a jour les info de la critique
        $critique->note = $request->input('rate');
        $critique->critique = $request->input('critique');
        $critique->save();
        //metre a jour la note du film
        $movie = Movies::where('id_movie', $critique->id_movie)->firstOrFail();
        $movie->avg_note = Critiques::where('id_movie', $movie->id_movie)->avg('note');
        $movie->save();
        return to_route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]);
    }

    public function delete(string $id)
    {
        $critique = Critiques::findOrFail($id);
        $this->authorize('delete',$critique);
        $movie = Movies::where('id_movie', $critique->id_movie)->firstOrFail();
        //supprimer crtique
            if ($critique->delete()) {
                return to_route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id])
                    ->with('success', 'Critique supprimée avec succès.');
            }
            
            return back()->with('error', 'Impossible de supprimer la critique.');
            
    }
}