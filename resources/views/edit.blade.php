@extends('layout.app')


@section('content')
<div id="rating">
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
            <span class="star">&#9733;</span>
        </div>

    <form action="{{ route('critique.update', ['id' => $critique->id_critique]) }}" method="post">
        

@csrf

<input type="text" name="rate" id="rate">
<textarea name="critique" id="critique" minlength="11"></textarea>

</select>

<button>Envoyer</button>

</form>
@endsection