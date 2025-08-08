@extends('layout.app')

@section('content')
<!-- affiche les dernieres actualités -->
@can('create', $news2)
<form action="{{ route('actualites.create',$news2,$news2) }}" method="POST" class="admin-refresh-form">
      @csrf
      <button type="submit" class="btn-refresh">Rafraîchir actualité</button>
  </form>
@endcan
    <h1 class="text-2xl font-bold mb-4">Actualités Cinéma</h1>

    <div class="news-container">
        @foreach($news as $new)
            <div class="news-card">
                <img src="https://image.tmdb.org/t/p/w500{{ $new->image }}" alt="{{ $new->title_news }}">
                <h2>{{ $new->title_news }}</h2>
                <p class="release-date">Sortie : {{ $new->release_date }}</p>
                <p>{{ $new->news }}</p>
            </div>
        @endforeach
    </div>
@endsection
