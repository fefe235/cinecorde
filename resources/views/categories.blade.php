@extends('layout.app')
@section('title', 'Catégories - Cinecorde')

@section('content')
<h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 1rem;"> Classement des films par catégorie</h1>
<!-- affiche les films par categories et par notes utilisation de alpine.js pour l'accordéon -->
@foreach ($categories as $categorie)
@php
    $filtered = $categorie->movies->sortByDesc('avg_note');
    $count = $filtered->count();
@endphp

    <div x-data="{ open: false }" style="margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 6px;">
        <button @click="open = !open" class="category-button">
            {{ $categorie->title_cat }} ({{ $count }} film{{ $count > 1 ? 's' : '' }})
            <svg :class="{ 'rotate': open }" class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div class="category-content" x-ref="content" :class="{ 'open': open }">
            @forelse ($filtered as $index => $movie)
                <div class="movie-card">
                    <a href="{{ route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]) }}">
                        <h3 style="font-size: 1.1rem; font-weight: bold;">
                            #{{ $index + 1 }} - {{ $movie->movie_title }} ({{ $movie->year }})
                        </h3>
                        <img src="{{ $movie->image }}" width="150" style="margin: 0.5rem 0;">
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

