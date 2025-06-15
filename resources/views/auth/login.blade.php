<!-- resources/views/auth/login.blade.php -->
@extends('layout.app')

@section('content')

    <form action="{{ route('auth.doLogin') }}" method="post">
        @csrf

        <input type="email" name="email" placeholder="Votre login">

        <input type="password" name="password" id="password">

        <button>Se connecter</button>

    </form>

@endsection
