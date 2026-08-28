@extends('layouts.app')

@section('title', 'Utenti — Connetto')

@section('content')
    <x-page-header icon="bi-people" title="Utenti" subtitle="Gestione degli accessi e dei ruoli" />

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('utenti.create') }}" class="btn btn-primary">Nuovo utente</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Ruolo</th>
                        <th>Collegamento</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($utenti as $utente)
                        <tr>
                            <td>{{ $utente->name }}</td>
                            <td>{{ $utente->email }}</td>
                            <td>
                                <span class="badge {{ match($utente->ruolo) {
                                    'admin' => 'text-bg-danger',
                                    'operatore' => 'text-bg-primary',
                                    default => 'text-bg-secondary',
                                } }}">
                                    {{ ucfirst($utente->ruolo) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                @if ($utente->ruolo === 'operatore')
                                    {{ $utente->operatore->nome ?? '— nessun operatore collegato —' }}
                                @elseif ($utente->ruolo === 'paziente')
                                    ID FileMaker: {{ $utente->filemaker_persona_id ?? '—' }}
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('utenti.edit', $utente) }}" class="btn btn-sm btn-outline-secondary">Modifica</a>
                                @if ($utente->id !== auth()->id())
                                    <form action="{{ route('utenti.destroy', $utente) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminare questo utente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
