@extends('layout.app')

@section('content')
<div class="container admin-container">
  @auth
  <form action="{{ route('actualites.create') }}" method="POST" class="admin-refresh-form">
      @csrf
      <button type="submit" class="btn-refresh">Rafraîchir actualité</button>
  </form>

  <div class="admin-movie-list">
    @foreach($movies as $movie)
      <div class="admin-movie-card">
        <a href="{{ route('movies.show', ['slug' => $movie->slug,'tmdb_id' => $movie->tmdb_id]) }}" class="admin-movie-link">
          <h3>{{ $movie->movie_title }} ({{ $movie->year }})</h3>
          <img src="{{ $movie->image }}" alt="{{ $movie->movie_title }}" />
        </a>
        <p><strong>Note : </strong>{{ $movie->avg_note }}/10</p>

        @if(Auth()->user()->role === "admin")
        <form action="{{ route('movies.delete', ['id' => $movie->id_movie]) }}" method="post" class="admin-delete-form">
          @csrf
          <button type="submit" class="btn-delete">Supprimer</button>
        </form>
        @endif
      </div>
    @endforeach
  </div>
  @endauth
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">enregistrer un admin</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('auth.registercreateAd') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">nom</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">mdp</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">confirmation mdp</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    s'enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
