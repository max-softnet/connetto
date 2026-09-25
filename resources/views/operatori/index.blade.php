@extends('layouts.app')

@section('title', 'Operatori — Connetto')

@section('content')
    <x-page-header icon="bi-person-badge" title="Operatori" subtitle="Operatori alimentati dagli appuntamenti importati da FileMaker" />

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover w-100 align-middle">
                    <thead>
                        <tr>
                            <th>Operatore</th>
                            <th>Criterio automazioni</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operatori as $operatore)
                            <tr>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: {{ $operatore->colore }};">
                                        {{ $operatore->nome }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $operatore->abilitato_automazioni ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $operatore->abilitato_automazioni ? 'Abilitato' : 'Disabilitato' }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('operatori.toggle-automazioni', $operatore) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            {{ $operatore->abilitato_automazioni ? 'Disabilita' : 'Abilita' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted">Nessun operatore ancora presente: vengono creati automaticamente al primo appuntamento importato da FileMaker.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-secondary small mt-4">
        <i class="bi bi-info-circle"></i>
        Gli operatori vengono creati automaticamente quando arriva da FileMaker un appuntamento con un nome operatore mai visto prima; qui non si creano o modificano manualmente.
        Un operatore <strong>disabilitato</strong> non è selezionabile come criterio "Operatore" nella configurazione delle automazioni (di default sono tutti abilitati).
    </div>
@endsection
