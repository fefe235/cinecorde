@extends('layout.app')

@section('content')

@if ($categoriesWithMovies)

<div class="row row-cols-1 row-cols-md-3 g-4">
<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
      <svg class="bi pe-none me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
      <span class="fs-4">Categories</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column">
    @foreach ($categoriesWithMovies as $category )
      <li class="nav-item">
                <a href="?id={{ $category->id_cat }}" 
                class="nav-link px-2 {{ request('id') == $category->id_cat ? 'fw-bold text-primary' : '' }}">
                    {{ $category->title_cat }}
                </a>
            
      </li>
      @endforeach
    </ul>
  </div>
  @endif
    <!-- 🔹 Affichage des films -->
@if (isset($filteredMovies) && $filteredMovies->count())

    
        @foreach ($filteredMovies as $movie)
            <div class="col">
                <div class="card h-10">
                    <img src="{{ asset($movie->image) }}" class="card-img-top" alt="{{ $movie->movie_title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $movie->movie_title }} ({{ $movie->year }})</h5>
                        <p class="card-text"><strong>Note :</strong> {{ $movie->avg_note }}/10</p>
                        <a href="{{ route('movies.show', ['slug' => $movie->slug, 'tmdb_id' => $movie->tmdb_id]) }}" class="btn btn-primary">Voir le film</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 🔹 Pagination -->
    <div class="align-center">
        {{ $filteredMovies->appends(['id' => $selectedCategoryId])->links() }}
    </div>
@else
    <p class="text-muted">Aucun film dans cette catégorie.</p>
@endif

@endsection

