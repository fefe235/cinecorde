@extends('layout.app')

@section('content')
  @can('create', $usersClass)
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
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name') }}" required autocomplete="name">

        @error('name')
      <span class="invalid-feedback" role="alert">
      <strong>{{ $message }}</strong>
      </span>
      @enderror
        </div>
      </div>
      <div class="row mb-3">
        <label for="role" class="col-md-4 col-form-label text-md-end">role</label>

        <div class="col-md-6">
        <input id="role" type="number" class="form-control @error('role') is-invalid @enderror" name="role"
        value="{{ old('role') }}" max="1" min="0" required autocomplete="role">

        @error('role')
      <span class="invalid-feedback" role="alert">
      <strong>{{ $message }}</strong>
      </span>
      @enderror
        </div>
      </div>
      <div class="row mb-3">
        <label for="email" class="col-md-4 col-form-label text-md-end">email</label>

        <div class="col-md-6">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
        value="{{ old('email') }}" required autocomplete="email">

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
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
        name="password" required autocomplete="new-password">

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
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
        autocomplete="new-password">
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
  @endcan

  <!-- affiche tout les utilisateur par nombre de likes -->
  <div class="container">
    <ol class="top-critique-list">
    @foreach($users as $index => $user)
      <li class="top-critique-item">
      <span class="top-critique-rank">{{ $index + 1 }}</span>
      <h3>{{ $user->name }} — {{ $user->nbr_like_total }} like{{ $user->nbr_like_total > 1 ? 's' : '' }}</h3>
      
      @can('delete', $user)
            <form action="{{ route('users.destroy', ['id' => $user->user_id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">Supprimer</button>
            </form>
        @endcan
        </li>
    @endforeach
    </ol>
  </div>
  <div class="align-center">{{ $users->links() }}</div>
@endsection