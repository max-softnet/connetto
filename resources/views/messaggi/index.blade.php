@extends('layouts.app')

@section('title', 'Messaggi — Connetto')

@section('content')
    <x-page-header icon="bi-envelope" title="Messaggi" subtitle="Log dei messaggi composti" />

    @forelse ($messaggi as $messaggio)
        <div class="card mb-3">
            <div class="card-body d-flex align-items-start gap-3">
                <span class="badge {{ match($messaggio->canale) {
                    'email' => 'text-bg-info',
                    'whatsapp' => 'text-bg-success',
                    default => 'text-bg-secondary',
                } }} text-uppercase">
                    {{ $messaggio->canale }}
                </span>

                <div class="flex-grow-1">
                    <div class="fw-medium">
                        {{ $messaggio->appuntamento?->titolo ?? '— appuntamento eliminato —' }}
                        <span class="text-muted small fw-normal">→ {{ $messaggio->destinatario }}</span>
                    </div>
                    @if ($messaggio->oggetto)
                        <div class="text-muted small">Oggetto: {{ $messaggio->oggetto }}</div>
                    @endif
                    <p class="mb-1 small" style="white-space: pre-line;">{{ $messaggio->corpo }}</p>
                    <div class="text-muted small">{{ $messaggio->created_at->format('d/m/Y H:i') }}</div>
                    @if ($messaggio->stato === 'fallito' && $messaggio->errore)
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $messaggio->errore }}
                        </div>
                    @endif
                </div>

                <span class="badge rounded-pill {{ match($messaggio->stato) {
                    'inviato' => 'text-bg-success',
                    'fallito' => 'text-bg-danger',
                    default => 'text-bg-warning',
                } }}">
                    {{ ucfirst($messaggio->stato) }}
                </span>

                <div class="d-flex flex-column gap-2">
                    @if ($messaggio->canale === 'whatsapp' && $messaggio->stato !== 'inviato')
                        <form action="{{ route('messaggi.invia', $messaggio) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="bi bi-send"></i>
                                {{ $messaggio->stato === 'fallito' ? 'Riprova invio' : 'Invia ora' }}
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('messaggi.destroy', $messaggio) }}" method="POST" onsubmit="return confirm('Eliminare questo messaggio?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Nessun messaggio ancora salvato.</p>
    @endforelse
@endsection
