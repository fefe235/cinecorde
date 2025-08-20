@extends('layout.app')

@section('content')
    <div class="container">
        <!-- affiche le détail d'un film -->
        <div class="movie-header">
            <img src="{{ asset($movie->image) }}" alt="{{ $movie->movie_title }}">
            <div class="movie-details">
                <h3>{{ $movie->movie_title }} ({{ $movie->year }})</h3>
                <p><strong>Note :</strong> {{ $movie->avg_note }}/10</p>
                <p><strong>Synopsis :</strong> {{ $movie->synopsis }}</p>
                <p><strong>Casting :</strong> {{ $movie->casting }}</p>
                @if($movie->trailler)
                    <p><a href="{{ $movie->trailler }}" target="_blank">🎬 Voir la bande-annonce</a></p>
                @endif
                @auth
                <form action="{{ route('movies.favorite', $movie->id_movie) }}" method="POST">
                    @csrf
                    <button type="submit">
                        {{ auth()->user()->favoriteMovies->contains($movie->id_movie) ? '💔' : '❤️' }}
                    </button>
                </form>
                @endauth
                @can('delete', $movie)
                    <form action="{{ route('movies.delete', $movie, $movie->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Supprimer</button>
                    </form>
                @endcan
            </div>
        </div>
        <!-- bouton supprimer pour l'admin -->
        @auth
            <!-- note avec des étoile lié au formulaire -->
            <div id="rating">
                @for ($i = 1; $i <= 10; $i++)
                    <span class="star" data-value="{{ $i }}">&#9733;</span>
                @endfor
            </div>
            <!-- formulaire de la critique -->
            <form action="{{ route('critique.create') }}" method="post">
                @csrf
                <input type="hidden" name="rate" id="rate">
                <input type="hidden" name="id_movie" value="{{ $movie->id_movie }}">
                <input type="hidden" name="id_user" value="{{ $userId }}">
                <textarea name="critique" id="critique" minlength="11" placeholder="Votre critique..."></textarea>
                <button type="submit">Envoyer</button>
            </form>
        @endauth
        <!-- afficher les critiques -->
        @if ($critiques)
            @foreach ($critiques as $critique)
                @if($critique->id_movie === $movie->id_movie)
                    <div class="critique">
                        <p><strong>Par :</strong> {{ $critique->user->name ?? 'Utilisateur inconnu' }}</p>
                        <h1>{{ $critique->note }}</h1>
                        <p>{{ $critique->critique }}</p>
                        <p>Likes : {{ $critique->likes->count() }}</p>
                        <!-- verifie si chaque utilisateur a liker ou non chaques critiques -->
                        @auth
                            @php
                                $userLiked = isset($userLikedCritiques[$critique->id_critique]);
                            @endphp
                            <!-- affiche ensuite le bouton like ou dislike -->
                            @if(!$userLiked)
                                <form action="{{ route('critique.like', ['id' => $critique->id_critique]) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    <button type="submit" class="like-button">👍 Like</button>
                                </form>
                            @else
                                <form action="{{ route('critique.dislike', ['id' => $critique->id_critique]) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    <button type="submit" class="dislike-button">👎 Dislike</button>
                                </form>
                            @endif
                            <!-- supprimer critique -->
                            @can("update", $critique)
                                <a href="{{ route('critique.edit', ['id' => $critique->id_critique]) }}">Modifier</a>
                                <form action="{{ route('critique.delete', ['id' => $critique->id_critique]) }}" method="post"
                                    style="display:inline;">
                                    @csrf
                                    <button type="submit">Supprimer</button>
                                </form>
                            @endcan
                        @endauth
                    </div>
                @endif
            @endforeach
        @endif
    </div>
     <!-- Pagination -->
 <div class="align-center">{{ $critiques->links() }}</div>
@endsection