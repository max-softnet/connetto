@extends('layouts.app')

@section('title', 'Calendario — ' . $data->translatedFormat('j F Y') . ' — Connetto')

@section('content')
    <x-page-header icon="bi-calendar3" title="Calendario" subtitle="Appuntamenti del giorno selezionato" />

    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <a href="{{ route('calendario.giorno', ['data' => $data->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">&laquo;</a>

        <form method="GET" action="{{ route('calendario.giorno') }}" class="d-flex align-items-center gap-2">
            <input
                type="date"
                name="data"
                value="{{ $data->format('Y-m-d') }}"
                class="form-control"
                onchange="this.form.submit()"
            >
        </form>

        <a href="{{ route('calendario.giorno', ['data' => $data->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">&raquo;</a>

        <a href="{{ route('calendario.giorno') }}" class="btn btn-link">Oggi</a>

        <span class="fw-medium text-capitalize">{{ $data->translatedFormat('l j F Y') }}</span>

        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('calendario.elenco', ['data' => $data->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm">Vista elenco</a>
            <a href="{{ route('calendario.mese', ['mese' => $data->format('Y-m')]) }}" class="btn btn-outline-secondary btn-sm">Vista mese</a>
        </div>
    </div>

    @forelse ($appuntamenti as $appuntamento)
        <div class="list-group mb-2">
            <div class="list-group-item d-flex align-items-center gap-3 py-3">
                <div class="rounded-pill" style="width: 6px; align-self: stretch; background-color: {{ $appuntamento->operatoreAppuntamento->colore ?? $appuntamento->tipoAppuntamento->colore ?? '#adb5bd' }};"></div>

                <div class="text-muted small" style="width: 5.5rem; flex-shrink: 0;">
                    {{ \Illuminate\Support\Carbon::parse($appuntamento->ora_inizio)->format('H:i') }}
                    &ndash;
                    {{ \Illuminate\Support\Carbon::parse($appuntamento->ora_fine)->format('H:i') }}
                </div>

                <div class="flex-grow-1">
                    <div class="fw-medium">{{ $appuntamento->titolo }}</div>
                    @if ($appuntamento->descrizione)
                        <div class="text-muted small">{{ $appuntamento->descrizione }}</div>
                    @endif
                </div>

                @if ($appuntamento->operatore)
                    <span
                        class="badge rounded-pill"
                        style="background-color: {{ $appuntamento->operatoreAppuntamento->colore ?? '#adb5bd' }};"
                    >
                        {{ $appuntamento->operatore }}
                    </span>
                @endif

                <span
                    class="badge rounded-pill"
                    style="background-color: {{ $appuntamento->tipoAppuntamento->colore ?? '#adb5bd' }};"
                >
                    {{ $appuntamento->tipo }}
                </span>

                <span class="badge rounded-pill {{ $appuntamento->status === 'annullato' ? 'text-bg-danger' : 'text-bg-success' }}">
                    {{ ucfirst($appuntamento->status) }}
                </span>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('messaggi.crea', $appuntamento) }}" class="btn btn-sm btn-outline-primary">
                        Invia messaggio
                    </a>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">Nessun appuntamento per questo giorno.</p>
    @endforelse
@endsection
