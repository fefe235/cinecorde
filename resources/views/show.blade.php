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
    @auth
    @if(Auth()->user()->role=== "admin")
        <form action="{{ route('movies.delete', ['id' =>$movie->id_movie]) }}" method="post">
        @csrf
        <button>Supprimer</button>
    </form>
    @endif
        <div id="rating">
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
        </div>

        <form action="{{ route('critique.create') }}" method="post">

            @csrf

            <input type="text" name="rate" id="rate" style="display:none">
            <input type="text" name="id_movie" id="id_movie" value="{{ $movie->id_movie }}" style="display:none">
            <input type="text" name="id_user" id="id_user" value="{{ Auth::user()->user_id}}" style="display:none">
            <textarea name="critique" id="critique" minlength="11"></textarea>

            </select>

            <button>Envoyer</button>

        </form>
    @endauth
    @if ($critiques)
    @foreach ($critiques as $critique)
    @if($critique->id_movie === $movie->id_movie)
    <p><strong>Par :</strong> {{ $critique->user->name ?? 'Utilisateur inconnu' }}</p>

    <h1>{{ $critique->note }}</h1>
   
    
    <p>{{ $critique->critique }}</p>
    <p> Likes : {{ $critique->likes->count() }}</p>

    @auth
    @php
    $userLiked = \App\Models\Like::where('user_id', Auth::user()->user_id)
        ->where('critique_id', $critique->id_critique)
        ->exists();
@endphp

@if(!$userLiked)
    <form action="{{ route('critique.like', ['id' => $critique->id_critique]) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit">👍 Like</button>
    </form>
@else
    <form action="{{ route('critique.dislike', ['id' => $critique->id_critique]) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit">👎 Dislike</button>
    </form>
@endif

    
    @if(Auth::check() && $critique->id_user === Auth::user()->user_id)

        <a href="{{ route('critique.edit', ['id' => $critique->id_critique]) }}">Modifier</a>

        <form action="{{ route('critique.delete', ['id' => $critique->id_critique]) }}" method="post">
            @csrf
            <button>Supprimer</button>
        </form>
        @endauth

    @endif
    @endif
    @endforeach
    @endif
    @endsection