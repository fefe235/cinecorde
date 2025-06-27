@extends('layout.app')

@section('content')
<div class="container mt-4">
    <h2>Conversation avec {{ $user->name }}</h2>

    <div class="chat-box border rounded p-3 mb-4" style="max-height: 400px; overflow-y: auto; background-color: #f8f9fa;">
        @foreach ($messages as $msg)
            <div class="mb-3 d-flex flex-column 
                {{ $msg->from_id == Auth::id() ? 'align-items-end' : 'align-items-start' }}">
                
                <small class="text-muted">{{ $msg->created_at->format('H:i') }}</small>

                @if($user->user_id === $msg->from_id)
                    <div class="bg-light p-2 rounded" style="max-width: 70%;">
                        <p class="mb-1 text-primary">de {{ $user->name }}</p>
                        <p class="mb-0">{{ $msg->message }}</p>
                    </div>
                @else
                    <div class="bg-primary text-white p-2 rounded" style="max-width: 70%;">
                        <p class="mb-0">{{ $msg->message }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <form action="{{ route('chat.store', ['user_id' => $user->user_id]) }}" method="POST">
        @csrf
        <input type="hidden" name="to_id" value="{{ $user->user_id }}">
        <div class="mb-3">
            <label for="message" class="form-label">Votre message</label>
            <textarea 
                class="form-control" 
                id="message" 
                name="message" 
                rows="3" 
                placeholder="Écrivez votre message ici..." 
                required
            ></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>
@endsection
