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
                <!-- Form fields (name, role, email, password, confirmation) -->
                <!-- ... Ton code formulaire inchangé ... -->
                <div class="row mb-0">
                  <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">s'enregistrer</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endcan

  <!-- Barre de recherche -->
  <div class="container mb-4">
    <form action="{{ route('top_critique') }}" method="GET" class="d-flex">
      <input type="text" name="search" class="form-control me-2" 
             placeholder="Rechercher par nom..." value="{{ request('search') }}">
      <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
    </form>
  </div>

  <!-- Liste utilisateurs avec accordéon Alpine.js -->
  <div class="container">
  <ol class="top-critique-list list-decimal pl-4">
    @foreach($users as $user)
    <li class="top-critique-item mb-3" x-data="{ open: false }">
      <div 
        class="d-flex justify-content-between align-items-center cursor-pointer bg-light p-2 rounded"
        @click="open = !open"
        :aria-expanded="open.toString()"
        aria-controls="user-{{ $user->user_id }}"
        role="button"
        tabindex="0"
        @keydown.enter="open = !open"
        @keydown.space.prevent="open = !open"
      >
        <div>
          <span class="top-critique-rank fw-bold me-2">#{{ $user->rank }}</span>
          <span class="fw-semibold">{{ $user->name }}</span>
        </div>

        <div class="d-flex align-items-center">
          <span class="me-2 text-secondary">
            {{ $user->nbr_like_total }} like{{ $user->nbr_like_total > 1 ? 's' : '' }}
          </span>
          <!-- Icône flèche Bootstrap -->
          <i class="bi bi-chevron-right transition"
             :class="open ? 'rotate-90' : ''"></i>
        </div>
      </div>

      <div
        x-show="open"
        x-transition
        id="user-{{ $user->user_id }}"
        class="mt-2 ms-4"
        style="display: none;"
      >
          @if($user->favoriteMovies->isEmpty())
            <p><em>Aucun favori.</em></p>
          @else
            <ul class="list-disc ml-5">
              @foreach($user->favoriteMovies as $movie)
              <li class="mb-2">
                <a href="{{ route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]) }}" class="text-blue-600 underline">
                  {{ $movie->movie_title }}
                </a>
                {{-- Ici tu peux ajouter les critiques et likes --}}
                @php
                  $userCritiques = $movie->critiques->where('id_user', $user->user_id);
                @endphp

                @if($userCritiques->isEmpty())
                  <p class="ml-4"><em>Aucune critique postée pour ce film.</em></p>
                @else
                  <ul class="ml-6 text-sm text-gray-700">
                    @foreach($userCritiques as $critique)
                      <li class="mb-1">
                        <strong>Likes :</strong> {{ $critique->likes_count ?? 0 }}<br>
                        <strong>Note :</strong> {{ $critique->note }}
                        <strong>Critique :</strong> {{ $critique->critique }}
                      </li>
                    @endforeach
                  </ul>
                @endif
              </li>
              @endforeach
            </ul>
          @endif

          @can('delete', $user)
            <form action="{{ route('users.destroy', ['id' => $user->user_id]) }}" method="POST" class="mt-2">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
            </form>
          @endcan
        </div>
      </li>
      @endforeach
    </ol>
  </div>
  

  <div class="align-center mt-4">
    {{ $users->appends(request()->query())->links() }}
  </div>

@endsection
