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
    public function registercreate(Request $request){
        $request->validate([
            'email' => 'required',
            'name'=>'required',
            'password'=>'required|min:4'
        ]);

        User::create([
                    'email' => $request->input("email"),
                    'name' => $request->input('name'),
                    'nbr_like_total'=> 0,
                    'password' => Hash::make($request->input("password"))
        ]);
        return redirect()->route('auth.login');
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

        return redirect()->intended(route('movies'));
    }

    return back()->withErrors([
        'email' => 'L’email ou le mot de passe est invalide'
    ]);
}
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return to_route('movies');
}
}
