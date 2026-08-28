@extends('layouts.app')

@section('title', 'Log WhatsApp — Connetto')

@section('content')
    <x-page-header icon="bi-whatsapp" title="Log WhatsApp" subtitle="Registro tecnico di ogni chiamata all'API di Meta" />

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabella-log-whatsapp" class="table table-striped table-hover w-100 align-middle">
                    <thead>
                        <tr>
                            <th>Data/ora</th>
                            <th>Esito</th>
                            <th>HTTP</th>
                            <th>Messaggio</th>
                            <th>Destinatario</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($log as $voce)
                            <tr>
                                <td>{{ $voce->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <span class="badge {{ $voce->esito === 'successo' ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ ucfirst($voce->esito) }}
                                    </span>
                                </td>
                                <td>{{ $voce->risposta_status ?? '—' }}</td>
                                <td>{{ $voce->messaggio?->appuntamento?->titolo ?? '—' }}</td>
                                <td>{{ $voce->messaggio?->destinatario ?? '—' }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        onclick="mostraDettagliLog({{ $voce->id }})"
                                    >
                                        Dettagli
                                    </button>
                                    <script type="application/json" id="dati-log-{{ $voce->id }}">
                                        {{ json_encode([
                                            'richiesta' => $voce->richiesta,
                                            'risposta' => $voce->risposta,
                                        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
                                    </script>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modale-dettagli-log" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h6">Dettaglio chiamata WhatsApp</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="small text-muted mb-1">Richiesta</div>
                        <pre class="small bg-light border rounded p-2 mb-0" id="modale-richiesta"></pre>
                    </div>
                    <div>
                        <div class="small text-muted mb-1">Risposta</div>
                        <pre class="small bg-light border rounded p-2 mb-0" id="modale-risposta"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostraDettagliLog(id) {
            const dati = JSON.parse(document.getElementById('dati-log-' + id).textContent);
            document.getElementById('modale-richiesta').textContent = JSON.stringify(dati.richiesta, null, 2);
            document.getElementById('modale-risposta').textContent = JSON.stringify(dati.risposta, null, 2);
            new bootstrap.Modal(document.getElementById('modale-dettagli-log')).show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#tabella-log-whatsapp', {
                language: {
                    search: 'Cerca:',
                    lengthMenu: 'Mostra _MENU_ elementi',
                    info: 'Vista da _START_ a _END_ di _TOTAL_ elementi',
                    infoEmpty: 'Nessun elemento da visualizzare',
                    infoFiltered: '(filtrati da _MAX_ elementi totali)',
                    zeroRecords: 'Nessun elemento trovato',
                    emptyTable: 'Nessuna chiamata registrata',
                    paginate: {
                        first: 'Primo',
                        last: 'Ultimo',
                        next: 'Successivo',
                        previous: 'Precedente',
                    },
                },
                columnDefs: [
                    { orderable: false, targets: [5] },
                ],
                order: [[0, 'desc']],
            });
        });
    </script>
@endsection
