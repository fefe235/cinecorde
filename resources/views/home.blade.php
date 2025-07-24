@extends('layout.app')

@section('content')

<!-- affiche les films par note -->
<div class="top-films">
  @foreach($movies as $index => $movie)
    <div class="top-film-card">
      <div class="top-rank-badge">Top #{{ $loop->iteration + ($movies->currentPage() - 1) * $movies->perPage() }}. {{ $movie->title }}</div>
      <a href="{{ route('movies.show', ['slug' => $movie->slug,'tmdb_id' => $movie->tmdb_id]) }}">
        <img src="{{ $movie->image }}" alt="{{ $movie->movie_title }}">
        <h3>{{ $movie->movie_title }} ({{ $movie->year }})</h3>
        <p><strong>Note :</strong> {{ $movie->avg_note }}/10</p>
      </a>
      
    </div>
  @endforeach
  </div>
 <!-- Pagination -->
 <div class="align-center">{{ $movies->links() }}</div>

 
@endsection
