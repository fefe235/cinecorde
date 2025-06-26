@extends('layout.app')

@section('content')
    <div class="login-container">
        <form action="{{ route('auth.doLogin') }}" method="post">
            @csrf

            <input type="email" name="email" placeholder="Votre Adresse email" required>

            <input type="password" name="password" id="password" placeholder="Votre mot de passe" required>
            @if ($errors->has('login_error'))
                <div class="alert alert-danger">
                    {{ $errors->first('login_error') }}
                </div>
            @endif
            <button type="submit">Se connecter</button>
        </form>
    </div>
@endsection