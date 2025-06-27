@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Choisissez un utilisateur pour discuter</h2>
    <div class="list-group">
        @foreach($users as $user)
            <a href="{{ route('chat.show', ['to_id' => $user->user_id]) }}" 
               class="list-group-item list-group-item-action">
                Discuter avec {{ $user->name }}
            </a>
        @endforeach
    </div>
</div>
@endsection
