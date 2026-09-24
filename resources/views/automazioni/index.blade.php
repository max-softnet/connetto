@extends('layouts.app')

@section('title', 'Automazioni — Connetto')

@section('content')
    <x-page-header icon="bi-lightning-charge" title="Automazioni" subtitle="Invio automatico dei messaggi in base agli appuntamenti" />

    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('log-automazioni.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history"></i>
            Storico esecuzioni
        </a>
        <form action="{{ route('automazioni.esegui') }}" method="POST" onsubmit="return confirm('Eseguire ora tutte le automazioni attive? Quelle già eseguite oggi verranno saltate automaticamente.');">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-play-fill"></i>
                Esegui ora
            </button>
        </form>
        <a href="{{ route('automazioni.create') }}" class="btn btn-primary">Nuova automazione</a>
    </div>

    @if (session('risultatiEsecuzione'))
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Risultato esecuzione</h2>
                <ul class="list-group list-group-flush">
                    @foreach (session('risultatiEsecuzione') as $riga)
                        <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                            <div>
                                <span class="badge text-bg-light border me-2">{{ $riga['automazione'] }}</span>
                                @if ($riga['appuntamento'])
                                    <strong>{{ $riga['appuntamento'] }}</strong>:
                                @endif
                                {{ $riga['esito'] }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @forelse ($automazioni as $automazione)
        @php $eseguitaOggi = $automazione->eseguitaOggi(); @endphp
        <div class="card mb-3">
            <div class="card-body d-flex align-items-start gap-3">
                <span class="badge {{ $automazione->attiva ? 'text-bg-success' : 'text-bg-secondary' }}">
                    {{ $automazione->attiva ? 'Attiva' : 'Disattiva' }}
                </span>

                <div class="flex-grow-1">
                    <h2 class="h6 mb-1">{{ $automazione->nome }}</h2>
                    <div class="text-muted small">
                        Modello: <strong>{{ $automazione->modello->nome }}</strong>
                        ({{ strtoupper($automazione->modello->canale) }})
                    </div>
                    <div class="text-muted small">
                        Invia
                        {{ $automazione->giorni_prima == 0 ? 'lo stesso giorno' : $automazione->giorni_prima . ' giorno/i prima' }}
                        dell'appuntamento
                        @if ($automazione->tipo_appuntamento)
                            — solo tipo <strong>{{ $automazione->tipo_appuntamento }}</strong>
                        @else
                            — tutti i tipi
                        @endif
                    </div>
                    @if ($eseguitaOggi)
                        <div class="small mt-1">
                            <span class="badge text-bg-light border">
                                <i class="bi bi-check-circle"></i>
                                Eseguita oggi alle {{ $eseguitaOggi->eseguita_at->format('H:i') }} ({{ $eseguitaOggi->esito }})
                            </span>
                        </div>
                    @endif
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('automazioni.edit', $automazione) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
                    @if ($eseguitaOggi)
                        <form
                            action="{{ route('automazioni.esegui-forzata', $automazione) }}"
                            method="POST"
                            onsubmit="return confirm('Questa automazione è già stata eseguita oggi alle {{ $eseguitaOggi->eseguita_at->format('H:i') }}. Eseguirla comunque?');"
                        >
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success w-100">Esegui comunque</button>
                        </form>
                    @endif
                    <form action="{{ route('automazioni.destroy', $automazione) }}" method="POST" onsubmit="return confirm('Eliminare questa automazione?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Nessuna automazione configurata.</p>
    @endforelse

    <div class="alert alert-secondary small mt-4">
        <i class="bi bi-info-circle"></i>
        Le automazioni vengono eseguite dal comando pianificato una volta al giorno (08:00) quando l'app è su un server con cron attivo.
        In locale puoi lanciarle a mano da terminale con <code>php artisan automazioni:esegui</code>.
        Ogni automazione può essere eseguita una sola volta al giorno: un'esecuzione già avvenuta oggi (automatica o manuale) viene saltata, a meno di forzarla con "Esegui comunque".
    </div>
@endsection
