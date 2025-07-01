<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ChatController extends Controller
{

    public function showChat($to_id)
    {
        $from_id = Auth::id();
    
        // Vérifie que le destinataire existe
        $user = User::findOrFail($to_id);
    
        // Récupère les messages entre les deux utilisateurs
        $messages = Chat::where(function ($query) use ($from_id, $to_id) {
            $query->where('from_id', $from_id)->where('to_id', $to_id);
        })->orWhere(function ($query) use ($from_id, $to_id) {
            $query->where('from_id', $to_id)->where('to_id', $from_id);
        })->orderBy('created_at')->get();
    
        return view('chat.conversation', compact('messages', 'user'));
    }
    
    public function index()
    {
        $user = User::where('user_id', '!=', auth()->id())->get();
        // $messages = Chat::where(function ($q) use ($user) {
        //         $q->where('from_id', Auth::id())->where('to_id', $user->id);
        //     })->orWhere(function ($q) use ($user) {
        //         $q->where('from_id', $user->id)->where('to_id', Auth::id());
        //     })
        //     ->orderBy('created_at')
        //     ->get();

        return view('chat.index', [
            'users' => $user
        ]);
    }

    public function store(Request $request)
    {
        //mets les messages dans la base de donnees
        $user_id = $request->input('to_id');
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        Chat::create([
            'from_id' => Auth::id(),
            'to_id' => $user_id,
            'message' => $request->message,
        ]);

        return redirect()->route('chat.show', ['to_id' => $user_id]);
}
}