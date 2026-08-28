@extends('layouts.app')

@section('title', 'Modelli messaggio — Connetto')

@section('content')
    <x-page-header icon="bi-chat-left-text" title="Modelli messaggio" subtitle="Gestisci i modelli email e SMS" />

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('modelli-messaggio.create') }}" class="btn btn-primary">Nuovo modello</a>
    </div>

    @forelse ($modelli as $modello)
        <div class="card mb-3">
            <div class="card-body d-flex align-items-start gap-3">
                <span class="badge {{ match($modello->canale) {
                    'email' => 'text-bg-info',
                    'whatsapp' => 'text-bg-success',
                    default => 'text-bg-secondary',
                } }} text-uppercase">
                    {{ $modello->canale }}
                </span>

                <div class="flex-grow-1">
                    <h2 class="h6 mb-1">{{ $modello->nome }}</h2>
                    @if ($modello->oggetto)
                        <div class="text-muted small mb-1">Oggetto: {{ $modello->oggetto }}</div>
                    @endif
                    <p class="mb-1 small text-body-secondary" style="white-space: pre-line;">{{ $modello->corpo }}</p>
                    @if ($modello->whatsapp_template_nome)
                        <span class="badge text-bg-light border">
                            <i class="bi bi-whatsapp text-success"></i>
                            Template: {{ $modello->whatsapp_template_nome }} ({{ $modello->whatsapp_template_lingua }})
                        </span>
                    @endif
                    @if ($modello->tipo_appuntamento)
                        <span class="badge text-bg-light border">{{ $modello->tipo_appuntamento }}</span>
                    @endif
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('modelli-messaggio.edit', $modello) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
                    <form action="{{ route('modelli-messaggio.destroy', $modello) }}" method="POST" onsubmit="return confirm('Eliminare questo modello?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Nessun modello di messaggio ancora creato.</p>
    @endforelse
@endsection
