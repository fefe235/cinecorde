@extends('layout.app')

@section('content')
<div class="container">
@foreach($movies as $movie)
    <div>
        
        <a href="{{ route('movies.show', ['slug' => $movie->slug,'tmdb_id' => $movie->tmdb_id]) }}"><h3>{{ $movie->movie_title }} ({{ $movie->year }})</h3>
        <img src="{{ $movie->image }}" width="150"></a>
        <p><strong>Note : </strong>{{ $movie->avg_note }}/10</p>
        
    </div>
@endforeach
</div>
@endsection