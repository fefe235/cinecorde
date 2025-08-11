<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use AuthorizesRequests;
    public function register()
    {
        return view('auth.register');

    }

    public function top_critique(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        //avoir les utilisateur avec le plus de like
        $users = $query->orderByDesc('nbr_like_total')->paginate(10);
        $usersClass = User::class;
        return view('topcritique', [
            'users' => $users,
            'usersClass' => $usersClass,
        ]);

    }
    public function registercreate(Request $request)
    {
        //formulaire d'inscription
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
            'role' => 'user',
            'nbr_like_total' => 0,
            'bool_like' => '0',
            'password' => Hash::make($request->input("password"))
        ]);
        return redirect()->route('auth.login');
    }
    public function registercreateAd(Request $request)
    {
        //formulaire d'inscription pour un admin
        $request->validate([
            'email' => 'required|unique:users,email',
            'name' => 'required|unique:users,name',
            'password' => 'required|min:4|confirmed',
            'role' => 'required|max:1'
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.unique' => 'Ce nom est déjà utilisé, merci d’en choisir un autre.',
            'email.required' => 'L’email est obligatoire.',
            'email.email' => 'Merci d’entrer un email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'role.required' => 'le role oublié 1 pour admin '
        ]);
        $this->authorize('create', User::class);

        User::create([
            'email' => $request->input("email"),
            'name' => $request->input('name'),
            'isAdmin' => $request->input('role'),
            'nbr_like_total' => 0,
            'bool_like' => '0',
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
        //verifie l'email et le pwd
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4'
        ]);

        $userEstValide = Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ]);
        //creer une session admin ou user
        if ($userEstValide) {
            $request->session()->regenerate();

            return redirect()->intended(route('movies'));
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
    //google OAUTH
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(uniqid()), // mot de passe aléatoire
                    'nbr_like_total' => 0,
                    'bool_like' => 0,
                ]
            );

            Auth::login($user);
            return redirect()->intended(route($user->role === 'admin' ? 'admin' : 'movies'));

        } catch (\Exception $e) {
            return redirect()->route('auth.login')->withErrors(['login_error' => 'Échec de la connexion avec Google.']);
        }
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Optionnel : autorisation (admin seulement ?)
        $this->authorize("delete", $user);

        $user->delete();

        return redirect()->route('top_critique')->with('success', 'Utilisateur supprimé.');
    }
}
