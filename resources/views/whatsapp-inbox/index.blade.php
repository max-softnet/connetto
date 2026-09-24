@extends('layouts.app')

@section('title', 'Casella WhatsApp — Connetto')

@section('content')
    <x-page-header icon="bi-chat-dots" title="Casella WhatsApp" subtitle="Messaggi ricevuti dai clienti" />

    @forelse ($righe as $riga)
        @php $conversazione = $riga['conversazione']; @endphp
        <a href="{{ route('whatsapp-inbox.mostra', $conversazione) }}" class="card mb-2 text-decoration-none text-body">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-person-circle fs-3 text-muted"></i>

                <div class="flex-grow-1" style="min-width: 0;">
                    <div class="fw-medium">
                        {{ $riga['appuntamento']->titolo ?? $conversazione->nome_contatto ?? $conversazione->numero }}
                        <span class="text-muted small fw-normal">{{ $conversazione->numero }}</span>
                    </div>
                    @if ($riga['ultimoMessaggio'])
                        <div class="text-muted small text-truncate">
                            @if ($riga['ultimoMessaggio']->direzione === 'out')
                                <i class="bi bi-arrow-return-right"></i>
                            @endif
                            {{ $riga['ultimoMessaggio']->corpo }}
                        </div>
                    @endif
                </div>

                <div class="text-muted small text-nowrap">
                    {{ $conversazione->ultimo_messaggio_at?->format('d/m/Y H:i') }}
                </div>
            </div>
        </a>
    @empty
        <p class="text-muted">Nessun messaggio ricevuto per ora.</p>
    @endforelse
@endsection
