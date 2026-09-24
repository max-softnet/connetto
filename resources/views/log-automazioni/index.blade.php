@extends('layouts.app')

@section('title', 'Storico esecuzioni — Connetto')

@section('content')
    <x-page-header icon="bi-clock-history" title="Storico esecuzioni" subtitle="Registro delle esecuzioni delle automazioni" />

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabella-log-automazioni" class="table table-striped table-hover w-100 align-middle">
                    <thead>
                        <tr>
                            <th>Data/ora</th>
                            <th>Automazione</th>
                            <th>Origine</th>
                            <th>Esito</th>
                            <th>Dettagli</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($log as $voce)
                            @php
                                $classeEsito = match ($voce->esito) {
                                    'successo' => 'text-bg-success',
                                    'parziale' => 'text-bg-warning',
                                    'fallito' => 'text-bg-danger',
                                    default => 'text-bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $voce->eseguita_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $voce->automazione->nome ?? '—' }}</td>
                                <td>{{ ucfirst($voce->origine) }}</td>
                                <td><span class="badge {{ $classeEsito }}">{{ str_replace('_', ' ', ucfirst($voce->esito)) }}</span></td>
                                <td>{{ $voce->dettagli }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#tabella-log-automazioni', {
                language: {
                    search: 'Cerca:',
                    lengthMenu: 'Mostra _MENU_ elementi',
                    info: 'Vista da _START_ a _END_ di _TOTAL_ elementi',
                    infoEmpty: 'Nessun elemento da visualizzare',
                    infoFiltered: '(filtrati da _MAX_ elementi totali)',
                    zeroRecords: 'Nessun elemento trovato',
                    emptyTable: 'Nessuna esecuzione registrata',
                    paginate: {
                        first: 'Primo',
                        last: 'Ultimo',
                        next: 'Successivo',
                        previous: 'Precedente',
                    },
                },
                order: [[0, 'desc']],
            });
        });
    </script>
@endsection
