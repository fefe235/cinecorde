@extends('layout.app')

@section('content')
<div class="container">
    <div>
        <h3>{{ $movie->movie_title }} ({{ $movie->year }})</h3>
        <img src="{{ $movie->image }}" width="150">
        <p><strong>Note : </strong>{{ $movie->avg_note }}/10</p>
        <p><strong>Synopsis : </strong>{{ $movie->synopsis }}</p>
        <p><strong>Casting : </strong>{{ $movie->casting }}</p>
        @if($movie->trailler)
            <p><a href="{{ $movie->trailler }}" target="_blank">🎬 Voir la bande-annonce</a></p>
        @endif
    </div>
</div>
@endsection