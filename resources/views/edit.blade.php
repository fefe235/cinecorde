@extends('layout.app')

@section('content')
<div class="container">
    <div id="rating">
        @for ($i = 1; $i <= $critique->note; $i++)
            <span class="star selected" data-value="{{ $i }}">&#9733;</span>
        @endfor
        @for ($i = $critique->note; $i <= 10; $i++)
            <span class="star" data-value="{{ $i }}">&#9733;</span>
        @endfor
    </div>

    <form action="{{ route('critique.update', ['id' => $critique->id_critique]) }}" method="POST">
        @csrf
        <input type="hidden" name="rate" id="rate" value="{{ $critique->note }}">
        <textarea name="critique" id="critique" minlength="11" placeholder="Votre critique...">{{ $critique->critique }}</textarea>
        <button type="submit">Envoyer</button>
    </form>
</div>

{{-- Script JS pour activer les étoiles --}}
@endsection
