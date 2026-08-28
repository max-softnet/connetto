@extends('layouts.app')

@section('title', ($automazione->exists ? 'Modifica' : 'Nuova') . ' automazione — Connetto')

@section('content')
    <x-page-header icon="bi-lightning-charge" :title="$automazione->exists ? 'Modifica automazione' : 'Nuova automazione'" />

    <form
        action="{{ $automazione->exists ? route('automazioni.update', $automazione) : route('automazioni.store') }}"
        method="POST"
        style="max-width: 560px;"
    >
        @csrf
        @if ($automazione->exists)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $automazione->nome) }}" required placeholder="es. Promemoria WhatsApp giorno prima">
        </div>

        <div class="mb-3">
            <label for="modello_id" class="form-label">Modello di messaggio</label>
            <select name="modello_id" id="modello_id" class="form-select" required>
                <option value="">— Seleziona —</option>
                @foreach ($modelli as $modello)
                    <option value="{{ $modello->id }}" {{ (int) old('modello_id', $automazione->modello_id) === $modello->id ? 'selected' : '' }}>
                        {{ $modello->nome }} ({{ strtoupper($modello->canale) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="giorni_prima" class="form-label">Giorni prima dell'appuntamento</label>
            <input
                type="number"
                name="giorni_prima"
                id="giorni_prima"
                class="form-control"
                min="0"
                max="60"
                value="{{ old('giorni_prima', $automazione->giorni_prima ?? 1) }}"
                required
            >
            <div class="form-text">0 = il giorno stesso dell'appuntamento, 1 = il giorno prima, ecc.</div>
        </div>

        <div class="mb-3">
            <label for="tipo_appuntamento" class="form-label">Tipo appuntamento <span class="text-muted small">(opzionale)</span></label>
            <select name="tipo_appuntamento" id="tipo_appuntamento" class="form-select">
                <option value="">— Qualsiasi tipo —</option>
                @foreach ($tipiAppuntamento as $tipo)
                    <option value="{{ $tipo->nome }}" {{ old('tipo_appuntamento', $automazione->tipo_appuntamento) === $tipo->nome ? 'selected' : '' }}>
                        {{ $tipo->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-check mb-4">
            <input
                type="checkbox"
                name="attiva"
                id="attiva"
                class="form-check-input"
                value="1"
                {{ old('attiva', $automazione->exists ? $automazione->attiva : true) ? 'checked' : '' }}
            >
            <label for="attiva" class="form-check-label">Attiva</label>
        </div>

        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="{{ route('automazioni.index') }}" class="btn btn-link">Annulla</a>
    </form>
@endsection
