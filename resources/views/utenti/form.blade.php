@extends('layouts.app')

@section('title', ($utente->exists ? 'Modifica' : 'Nuovo') . ' utente — Connetto')

@section('content')
    <x-page-header icon="bi-people" :title="$utente->exists ? 'Modifica utente' : 'Nuovo utente'" />

    <form
        action="{{ $utente->exists ? route('utenti.update', $utente) : route('utenti.store') }}"
        method="POST"
        style="max-width: 480px;"
    >
        @csrf
        @if ($utente->exists)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $utente->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $utente->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                Password
                @if ($utente->exists)
                    <span class="text-muted small">(lascia vuoto per non modificarla)</span>
                @endif
            </label>
            <input type="password" name="password" id="password" class="form-control" {{ $utente->exists ? '' : 'required' }}>
        </div>

        <div class="mb-3">
            <label for="ruolo" class="form-label">Ruolo</label>
            <select name="ruolo" id="ruolo" class="form-select" required onchange="aggiornaCampiRuolo()">
                <option value="admin" {{ old('ruolo', $utente->ruolo) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="operatore" {{ old('ruolo', $utente->ruolo) === 'operatore' ? 'selected' : '' }}>Operatore</option>
                <option value="paziente" {{ old('ruolo', $utente->ruolo) === 'paziente' ? 'selected' : '' }}>Paziente</option>
            </select>
        </div>

        <div class="mb-3" id="campo-operatore">
            <label for="operatore_id" class="form-label">Operatore collegato</label>
            <select name="operatore_id" id="operatore_id" class="form-select">
                <option value="">— Seleziona —</option>
                @foreach ($operatori as $operatore)
                    <option value="{{ $operatore->id }}" {{ (int) old('operatore_id', $utente->operatore_id) === $operatore->id ? 'selected' : '' }}>
                        {{ $operatore->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4" id="campo-paziente">
            <label for="filemaker_persona_id" class="form-label">ID paziente FileMaker</label>
            <input
                type="number"
                name="filemaker_persona_id"
                id="filemaker_persona_id"
                class="form-control"
                value="{{ old('filemaker_persona_id', $utente->filemaker_persona_id) }}"
            >
            <div class="form-text">L'identificativo persona (campo <code>idp</code>) che arriva da FileMaker, per collegare l'account ai suoi appuntamenti.</div>
        </div>

        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="{{ route('utenti.index') }}" class="btn btn-link">Annulla</a>
    </form>

    <script>
        function aggiornaCampiRuolo() {
            const ruolo = document.getElementById('ruolo').value;
            document.getElementById('campo-operatore').style.display = ruolo === 'operatore' ? '' : 'none';
            document.getElementById('campo-paziente').style.display = ruolo === 'paziente' ? '' : 'none';
        }

        document.addEventListener('DOMContentLoaded', aggiornaCampiRuolo);
    </script>
@endsection
