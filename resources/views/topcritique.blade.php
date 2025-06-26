@extends('layout.app')

@section('content')
<div class="container">
  <ol class="top-critique-list">
    @foreach($users as $index => $user)
      <li class="top-critique-item">
        <span class="top-critique-rank">{{ $index + 1 }}</span>
        <h3>{{ $user->name }} — {{ $user->nbr_like_total }} like{{ $user->nbr_like_total > 1 ? 's' : '' }}</h3>
      </li>
    @endforeach
  </ol>
</div>
@endsection

