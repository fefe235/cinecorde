<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cinecorde</title>
    <link rel="icon" type="image/jpg" href="{{ asset('images/logo.jpg') }}">
    <!-- Fonts -->

    <link href="{{ asset('asset/css/main.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- Scripts -->
    <script src="//unpkg.com/alpinejs" defer></script>


</head>

<body>
    <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-around py-3 mb-4 border-bottom">
    <img src="{{ asset('asset/images/logo.jpg') }}" alt="logo" id="logo">

            <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
        <li><a href="{{ route('movies') }}" class="nav-link px-2">top films</a></li>
        <li><a href="{{ route('actualites') }}" class="nav-link px-2">actualités</a></li>
        <li><a href="{{ route('categories') }}" class="nav-link px-2">categories</a></li>
        <li><a href="{{ route('top_critique') }}" class="nav-link px-2">top critiques</a></li>
        <li><a href="{{ route('chat.index') }}" class="nav-link px-2">Messagerie</a></li>
        </ul>
        
        <div class="col-md-3 text-end" style="margin-top: 20px;">
            {{-- Formulaire de recherche --}}
            <form method="POST" action="{{ route('movies.search') }}" role="search" class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
                @csrf
                <div style="margin-bottom: 1em;display:flex;">
                    <input type="text" name="query" placeholder="Rechercher un film..." id="movie-search"
                        autocomplete="off">
                    <input type="text" name="id" id="movie-id" style="display: none;">
                    <div id="suggestions" style="position:relative;"></div>
                    <button type="submit">Rechercher</button>
                </div>
            </form>
        
            </div>
            <div class="col-md-3 text-end">
        {{-- Message d’erreur --}}
        @if(session('error'))
            <div style="color: red;">{{ session('error') }}</div>
        @endif
        <main>
            @if(session()->has('success'))
                {{ session()->get('success') }}
            @endif

                            @guest
                            @if (Route::has('auth.login'))
                                <button class="btn btn-outline-primary me-2">
                                    <a class="nav-link" href="{{ route('auth.login') }}"> se connecter </a>
                                </button>
                            @endif
                                @if (Route::has('auth.register'))
                                    <button class="btn btn-primary">
                                        <a class="nav-link" href="{{ route('auth.register') }}"> inscription</a>
                                    </button>
                                @endif
                            @else
                            <ul>
                                <li class="nav-item">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div aria-labelledby="navbarDropdown">
                                    <button class="btn btn-primary">
                                        <a class="nav-link" href="{{ route('auth.logout') }}" onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                    </button>

                                        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                                </ul>
                            @endguest
        </main>
       </div>
</header>
    @yield('content')

  <footer class="py-3 my-4">
    <p class="text-center text-body-secondary">© 2025 fait par F.Metge</p>
  </footer>

    <script src="{{ asset('asset/js/main.js') }}">
    </script>

</body>

</html>