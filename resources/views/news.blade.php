@extends('layout.app')
@section('content')
    <h1 class="text-2xl font-bold mb-4">Actualités Cinéma</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($news as $new)
            <div class="bg-white shadow p-2 rounded">
                <img src="https://image.tmdb.org/t/p/w500{{ $new->image }}" alt="{{ $new->title_news }}" class="w-full h-auto mb-2">
                <h2 class="text-lg font-semibold">{{ $new->title_news }}</h2>
                <p class="text-sm text-gray-600">Sortie : {{ $new->release_date }}</p>
                <p>{{ $new->news }}</p>
            </div>
        @endforeach
    </div>
@endsection