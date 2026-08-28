@extends('layouts.app')

@section('title', 'Nuovo messaggio — Connetto')

@section('content')
    <x-page-header icon="bi-envelope" title="Nuovo messaggio" />

    <p class="text-muted mb-4">
        Per <strong>{{ $appuntamento->titolo }}</strong> —
        {{ \Illuminate\Support\Carbon::parse($appuntamento->data)->translatedFormat('j F Y') }},
        {{ \Illuminate\Support\Carbon::parse($appuntamento->ora_inizio)->format('H:i') }}
    </p>

    <div class="row mb-4">
        <div class="col-sm-6">
            <div class="text-muted small">Email</div>
            <div>{{ $appuntamento->email ?? '— non presente —' }}</div>
        </div>
        <div class="col-sm-6">
            <div class="text-muted small">Cellulare</div>
            <div>{{ $appuntamento->cellulare ?? '— non presente —' }}</div>
        </div>
    </div>

    @if ($modelli->isEmpty())
        <div class="alert alert-warning">
            Nessun modello di messaggio disponibile per questo tipo di appuntamento.
            <a href="{{ route('modelli-messaggio.create') }}">Creane uno</a>.
        </div>
    @else
        <form method="GET" action="{{ route('messaggi.crea', $appuntamento) }}" class="mb-4">
            <label for="modello_id" class="form-label">Modello</label>
            <select name="modello_id" id="modello_id" class="form-select" onchange="this.form.submit()">
                <option value="">— Scegli un modello —</option>
                @foreach ($modelli as $modello)
                    <option value="{{ $modello->id }}" {{ optional($modelloSelezionato)->id === $modello->id ? 'selected' : '' }}>
                        {{ $modello->nome }} ({{ strtoupper($modello->canale) }})
                    </option>
                @endforeach
            </select>
        </form>

        @if ($modelloSelezionato)
            <form method="POST" action="{{ route('messaggi.salva', $appuntamento) }}">
                @csrf
                <input type="hidden" name="modello_id" value="{{ $modelloSelezionato->id }}">

                @if ($modelloSelezionato->canale === 'email')
                    <div class="mb-3">
                        <label for="oggetto" class="form-label">Oggetto</label>
                        <input type="text" name="oggetto" id="oggetto" class="form-control" value="{{ old('oggetto', $oggetto) }}">
                    </div>
                @endif

                <div class="mb-3">
                    <label for="corpo" class="form-label">Testo</label>
                    <textarea
                        name="corpo"
                        id="corpo"
                        class="form-control"
                        rows="6"
                        {{ $modelloSelezionato->whatsapp_template_nome ? 'readonly' : '' }}
                    >{{ old('corpo', $corpo) }}</textarea>

                    @if ($modelloSelezionato->whatsapp_template_nome)
                        <div class="form-text">
                            Verrà inviato tramite il template WhatsApp approvato
                            <code>{{ $modelloSelezionato->whatsapp_template_nome }}</code> ({{ $modelloSelezionato->whatsapp_template_lingua }}).
                            Il testo qui sopra è solo un'anteprima: la formulazione esatta è quella approvata su Meta,
                            qui vengono passati solo i valori al posto delle variabili.
                        </div>
                    @endif
                </div>

                <p class="text-muted small">
                    @if ($modelloSelezionato->canale === 'whatsapp')
                        Il messaggio verrà salvato come bozza. Dopo il salvataggio potrai inviarlo subito dal log messaggi.
                    @else
                        Il messaggio verrà salvato come bozza: l'invio effettivo tramite email/SMS non è ancora collegato a un provider.
                    @endif
                </p>

                <button type="submit" class="btn btn-primary">Salva messaggio</button>
                <a href="{{ route('calendario.giorno', ['data' => $appuntamento->data->format('Y-m-d')]) }}" class="btn btn-link">Annulla</a>
            </form>
        @endif
    @endif
@endsection
