@extends('layout.app')
@section('title', 'Catégories - Cinecorde')

@section('content')
@if ($categoriesWithMovies)
<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-around py-3 mb-4 border-bottom">
<ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
   

        @foreach ($categoriesWithMovies as $category )

        <li>
                <a href="?id={{ $category->id_cat }}" 
                   class="nav-link px-2 {{ request('id') == $category->id_cat ? 'fw-bold text-primary' : '' }}">
                    {{ $category->title_cat }}
                </a>
            </li>

            
        @endforeach

        </ul>
</header>
@endif
       <!-- 🔹 Affichage des films -->
@if (isset($filteredMovies) && $filteredMovies->count())
    <h2>Films dans la catégorie : <strong>{{ $categoriesWithMovies->firstWhere('id_cat', $selectedCategoryId)?->title_cat }}</strong></h2>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach ($filteredMovies as $movie)
            <div class="col">
                <div class="card h-100">
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

