<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\User;
use Auth;
use finfo;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(){
            return view('auth.register');
        
    }
  
    public function top_critique(){

        $users = User::orderBy('nbr_like_total', 'desc')->get();
        return view('topcritique',[
            'users'=> $users
        ]);
    
}
    public function registercreate(Request $request){
        $request->validate([
            'email' => 'required|unique:users,email',
            'name' => 'required|unique:users,name',
            'password' => 'required|min:4|confirmed',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.unique' => 'Ce nom est déjà utilisé, merci d’en choisir un autre.',
            'email.required' => 'L’email est obligatoire.',
            'email.email' => 'Merci d’entrer un email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
        ]);

        User::create([
                    'email' => $request->input("email"),
                    'name' => $request->input('name'),
                    'role'=> 'user',
                    'nbr_like_total'=> 0,
                    'bool_like'=> '0',
                    'password' => Hash::make($request->input("password"))
        ]);
        return redirect()->route('auth.login');
    }
    public function registercreateAd(Request $request){
        $request->validate([
            'email' => 'required|unique:users,email',
            'name' => 'required|unique:users,name',
            'password' => 'required|min:4|confirmed',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.unique' => 'Ce nom est déjà utilisé, merci d’en choisir un autre.',
            'email.required' => 'L’email est obligatoire.',
            'email.email' => 'Merci d’entrer un email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
        ]);

        User::create([
                    'email' => $request->input("email"),
                    'name' => $request->input('name'),
                    'role'=> 'admin',
                    'nbr_like_total'=> 0,
                    'bool_like'=> '0',
                    'password' => Hash::make($request->input("password"))
        ]);
        return back()->with('success', 'Nouvel admin ajouté avec succès.');
    }
    public function login()
{
    return view('auth.login');
}

public function doLogin(Request $request)
{
    $request->validate([
        'email' => 'required',
        'password' => 'required|min:4'
    ]);

    $userEstValide = Auth::attempt([
        'email' => $request->input('email'),
        'password' => $request->input('password')
    ]);

    if ($userEstValide) {
        $request->session()->regenerate();

        $user = Auth::user()->role;
        if($user == 'user')
        {
        return redirect()->intended(route('movies'));
        }else{
        return redirect()->intended(route('admin'));

        }
    }

    return back()->withErrors([
        'login_error' => 'Email ou mot de passe incorrect.',
    ])->withInput();
}
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return to_route('movies');
}
}
