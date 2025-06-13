@extends('layout.app')
@section('content')
    <h1 class="text-2xl font-bold mb-4">Actualités Cinéma</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($films as $film)
            <div class="bg-white shadow p-2 rounded">
                <img src="https://image.tmdb.org/t/p/w500{{ $film['poster_path'] }}" alt="{{ $film['title'] }}" class="w-full h-auto mb-2">
                <h2 class="text-lg font-semibold">{{ $film['title'] }}</h2>
                <p class="text-sm text-gray-600">Sortie : {{ $film['release_date'] }}</p>
                <p>{{ $film['overview'] }}</p>
            </div>
        @endforeach
    </div>
@endsection