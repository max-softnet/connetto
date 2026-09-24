@extends('layouts.app')

@section('title', 'Conversazione — Connetto')

@section('content')
    <x-page-header
        icon="bi-chat-dots"
        :title="$appuntamento->titolo ?? $conversazione->nome_contatto ?? $conversazione->numero"
        :subtitle="$conversazione->numero"
    />

    @if ($appuntamento)
        <div class="alert alert-light border small mb-3">
            <i class="bi bi-calendar3"></i>
            Collegato all'appuntamento <strong>{{ $appuntamento->titolo }}</strong>
            del {{ $appuntamento->data->translatedFormat('j F Y') }}
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body" style="max-height: 60vh; overflow-y: auto;" id="thread-messaggi">
            @forelse ($conversazione->messaggi as $messaggio)
                <div class="d-flex mb-2 {{ $messaggio->direzione === 'out' ? 'justify-content-end' : 'justify-content-start' }}">
                    <div
                        class="p-2 px-3 rounded-3 {{ $messaggio->direzione === 'out' ? 'bg-primary text-white' : 'bg-light border' }}"
                        style="max-width: 75%;"
                    >
                        <div style="white-space: pre-line;">{{ $messaggio->corpo }}</div>
                        <div class="small {{ $messaggio->direzione === 'out' ? 'text-white-50' : 'text-muted' }} text-end mt-1">
                            {{ $messaggio->created_at->format('d/m/Y H:i') }}
                            @if ($messaggio->direzione === 'out' && $messaggio->stato)
                                · {{ ucfirst($messaggio->stato) }}
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Nessun messaggio.</p>
            @endforelse
        </div>
    </div>

    <form action="{{ route('whatsapp-inbox.rispondi', $conversazione) }}" method="POST" class="d-flex gap-2">
        @csrf
        <textarea name="corpo" class="form-control" rows="2" placeholder="Scrivi una risposta..." required>{{ old('corpo') }}</textarea>
        <button type="submit" class="btn btn-primary align-self-end">
            <i class="bi bi-send"></i>
            Invia
        </button>
    </form>

    <p class="text-muted small mt-2">
        <i class="bi bi-info-circle"></i>
        La risposta libera funziona solo entro 24 ore dall'ultimo messaggio ricevuto dal cliente; oltre serve un template approvato.
    </p>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const thread = document.getElementById('thread-messaggi');
            thread.scrollTop = thread.scrollHeight;
        });
    </script>
@endsection
