<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'cinecorde') }}</title>

    <!-- Fonts -->

    <link href="{{ asset('asset/css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>
<body>
<nav>
	<a href="{{ route('movies') }}">top films</a>
	<a href="{{ route('actualites') }}">actualité</a>
	<a href="{{ route('categories') }}">categorie</a>
    <div>
    {{-- Formulaire de recherche --}}
    <form method="POST" action="{{ route('movies.search') }}">
        @csrf
        <div style="margin-bottom: 1em;">
            <input type="text" name="query" placeholder="Rechercher un film..." id="movie-search" autocomplete="off">
            <input type="text" name="id" id="movie-id" style="display: none;">
            <div id="suggestions" style="position:relative;"></div>
            <button type="submit">Rechercher</button>
        </div>
    </form>

    {{-- Message d’erreur --}}
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    
    </div>

	
</nav>
@yield('content')


<script src="{{ asset('asset/js/main.js') }}">
    </script>

</body>
</html>