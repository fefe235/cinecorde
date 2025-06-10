<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'cinecorde') }}</title>

    <!-- Fonts -->

    <link href="{{ asset('asset/css/main.css') }}" rel="stylesheet">

    <!-- Scripts -->
</head>
<body>
<nav>
	<a href="{{ route('movies') }}">top films</a>
	<a href="{{ route('actualites') }}">actualité</a>
	<a href="{{ route('categories') }}">categorie</a>
    <div>
    {{-- Formulaire de recherche --}}
    <form method="POST" action="{{ route('movies.search') }}">
        @csrf
        <div style="margin-bottom: 1em;">
            <input type="text" name="query" placeholder="Rechercher un film..." id="movie-search" autocomplete="off">
            <input type="text" name="id" id="movie-id" >
            <div id="suggestions" style="position:relative;"></div>
            <button type="submit">Rechercher</button>
        </div>
    </form>

    {{-- Message d’erreur --}}
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    
    </div>

	
</nav>
@yield('content')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('movie-search');
    const input2 = document.getElementById('movie-id');
    const suggestions = document.createElement('div');
    suggestions.id = 'suggestions-list';
    suggestions.style.position = 'absolute';
    suggestions.style.border = '1px solid #ccc';
    suggestions.style.background = '#fff';
    suggestions.style.maxHeight = '200px';
    suggestions.style.overflowY = 'auto';
    suggestions.style.width = input.offsetWidth + 'px';
    suggestions.style.zIndex = 1000;
    suggestions.style.display = 'none';

    input.parentNode.style.position = 'relative';
    input.parentNode.appendChild(suggestions);

    let timer = null;

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timer);

        if (query.length < 2) {
            suggestions.style.display = 'none';
            return;
        }

        timer = setTimeout(() => {
            fetch(`/autocomplete-movies?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    if (!data.length) {
                        suggestions.style.display = 'none';
                        return;
                    }

                    data.forEach(movie => {
                        const div = document.createElement('div');
                        div.textContent = `${movie.title} (${movie.release_date.slice(0, 4) || 'N/A'})`;
                        div.style.padding = '5px';
                        div.style.cursor = 'pointer';

                        div.addEventListener('click', () => {
                            input.value = movie.title;
                            input2.value= movie.id;
                            suggestions.style.display = 'none';
                            // Optionnel : déclencher recherche ou redirection
                        });

                        div.addEventListener('mouseenter', () => {
                            div.style.backgroundColor = '#eee';
                        });
                        div.addEventListener('mouseleave', () => {
                            div.style.backgroundColor = '#fff';
                        });

                        suggestions.appendChild(div);
                    });

                    suggestions.style.display = 'block';
                })
                .catch(() => {
                    suggestions.style.display = 'none';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (e.target !== input) {
            suggestions.style.display = 'none';
        }
    });
});
</script>

</body>
</html>