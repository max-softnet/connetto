@extends('layouts.app')

@section('title', 'Elenco appuntamenti — ' . $data->translatedFormat('j F Y') . ' — Connetto')

@section('content')
    <x-page-header icon="bi-table" title="Elenco appuntamenti" subtitle="Elenco tabellare del giorno selezionato" />

    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <a href="{{ route('calendario.elenco', ['data' => $data->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">&laquo;</a>

        <form method="GET" action="{{ route('calendario.elenco') }}" class="d-flex align-items-center gap-2">
            <input
                type="date"
                name="data"
                value="{{ $data->format('Y-m-d') }}"
                class="form-control"
                onchange="this.form.submit()"
            >
        </form>

        <a href="{{ route('calendario.elenco', ['data' => $data->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">&raquo;</a>

        <a href="{{ route('calendario.elenco') }}" class="btn btn-link">Oggi</a>

        <span class="fw-medium text-capitalize">{{ $data->translatedFormat('l j F Y') }}</span>

        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('calendario.giorno', ['data' => $data->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm">Vista giorno</a>
            <a href="{{ route('calendario.mese', ['mese' => $data->format('Y-m')]) }}" class="btn btn-outline-secondary btn-sm">Vista mese</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabella-appuntamenti" class="table table-striped table-hover w-100 align-middle">
                    <thead>
                        <tr>
                            <th>Inizio</th>
                            <th>Fine</th>
                            <th>Titolo</th>
                            <th>Descrizione</th>
                            <th>Tipo</th>
                            <th>Operatore</th>
                            <th>Stato</th>
                            @if (auth()->user()->isAdmin())
                                <th>Azioni</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appuntamenti as $appuntamento)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($appuntamento->ora_inizio)->format('H:i') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($appuntamento->ora_fine)->format('H:i') }}</td>
                                <td>{{ $appuntamento->titolo }}</td>
                                <td>{{ $appuntamento->descrizione }}</td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: {{ $appuntamento->tipoAppuntamento->colore ?? '#adb5bd' }};">
                                        {{ $appuntamento->tipo }}
                                    </span>
                                </td>
                                <td>
                                    @if ($appuntamento->operatore)
                                        <span class="badge rounded-pill" style="background-color: {{ $appuntamento->operatoreAppuntamento->colore ?? '#adb5bd' }};">
                                            {{ $appuntamento->operatore }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $appuntamento->status === 'annullato' ? 'text-bg-danger' : 'text-bg-success' }}">
                                        {{ ucfirst($appuntamento->status) }}
                                    </span>
                                </td>
                                @if (auth()->user()->isAdmin())
                                    <td>
                                        <a href="{{ route('messaggi.crea', $appuntamento) }}" class="btn btn-sm btn-outline-primary">
                                            Invia messaggio
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#tabella-appuntamenti', {
                language: {
                    search: 'Cerca:',
                    lengthMenu: 'Mostra _MENU_ elementi',
                    info: 'Vista da _START_ a _END_ di _TOTAL_ elementi',
                    infoEmpty: 'Nessun elemento da visualizzare',
                    infoFiltered: '(filtrati da _MAX_ elementi totali)',
                    zeroRecords: 'Nessun appuntamento trovato',
                    emptyTable: 'Nessun appuntamento per questo giorno',
                    paginate: {
                        first: 'Primo',
                        last: 'Ultimo',
                        next: 'Successivo',
                        previous: 'Precedente',
                    },
                },
                columnDefs: [
                    @if (auth()->user()->isAdmin())
                        { orderable: false, targets: [7] },
                    @endif
                ],
                order: [[0, 'asc']],
            });
        });
    </script>
@endsection
