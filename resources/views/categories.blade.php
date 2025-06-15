@extends('layout.app') 
@section('title', 'categories cinecorde')

@section('content')
<h1 class="text-2xl font-bold mb-4">🎬 Films par catégories</h1>

@php
    use Illuminate\Support\Collection;
@endphp

@foreach ($categories as $categorie)
    @php
        $filtered = $movies->filter(fn($movie) => $movie->id_cat === $categorie->id_cat)->sortByDesc('avg_note');
        $count = $filtered->count();
    @endphp

    <div x-data="{ open: false }" class="mb-4 border rounded shadow">
        <button @click="open = !open" class="w-full text-left px-4 py-2 bg-gray-200 hover:bg-gray-300 font-semibold">
            {{ $categorie->title_cat }} ({{ $count }} film{{ $count > 1 ? 's' : '' }})
        </button>

        <div x-show="open" class="p-4 space-y-4">
            @forelse($filtered as $movie)
                <div class="border-b pb-2">
                    <a href="{{ route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]) }}">
                        <h3 class="text-lg font-bold">{{ $movie->movie_title }} ({{ $movie->year }})</h3>
                        <img src="{{ $movie->image }}" width="150" class="mt-1 mb-2">
                    </a>
                    <p><strong>Note :</strong> {{ $movie->avg_note }}/10</p>
                </div>
            @empty
                <p>Aucun film dans cette catégorie.</p>
            @endforelse
        </div>
    </div>
@endforeach
@endsection
