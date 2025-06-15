@extends('layout.app')

@section('content')
<div class="container">
@foreach($users as $user)
    <div>
        <ul>
            <li>
                <h3>pseado:{{ $user->name }}</h3>
                <h3>nombre total de like:{{ $user->nbr_like_total }}</h3>
            </li>
        </ul>
        
    </div>
@endforeach
</div>
@endsection