@extends('layouts.app')

@section('title', 'Calendario — ' . $mese->translatedFormat('F Y') . ' — Connetto')

@php
    $giorniSettimana = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
    $oggi = \Illuminate\Support\Carbon::today();
@endphp

@section('content')
    <x-page-header icon="bi-calendar3" title="Calendario" subtitle="Vista mensile degli appuntamenti" />

    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <a href="{{ route('calendario.mese', ['mese' => $mese->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-outline-secondary">&laquo;</a>
        <a href="{{ route('calendario.mese', ['mese' => $mese->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-outline-secondary">&raquo;</a>
        <a href="{{ route('calendario.mese') }}" class="btn btn-link">Oggi</a>
        <span class="fw-medium text-capitalize">{{ $mese->translatedFormat('F Y') }}</span>

        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('calendario.giorno', ['data' => $mese->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm">Vista giorno</a>
            <a href="{{ route('calendario.elenco', ['data' => $mese->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm">Vista elenco</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white align-top" style="table-layout: fixed;">
            <thead>
                <tr>
                    @foreach ($giorniSettimana as $nomeGiorno)
                        <th class="text-center small text-muted fw-normal py-2">{{ $nomeGiorno }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($settimane as $settimana)
                    <tr>
                        @foreach ($settimana as $giorno)
                            @php
                                $fuoriMese = $giorno->month !== $mese->month;
                                $appuntamentiGiorno = $appuntamentiPerGiorno->get($giorno->format('Y-m-d'), collect());
                                $daMostrare = $appuntamentiGiorno->take(3);
                                $altri = $appuntamentiGiorno->count() - $daMostrare->count();
                            @endphp
                            <td style="height: 6.5rem; {{ $fuoriMese ? 'background-color: #f8f9fa;' : '' }}">
                                <a
                                    href="{{ route('calendario.giorno', ['data' => $giorno->format('Y-m-d')]) }}"
                                    class="d-inline-flex align-items-center justify-content-center mb-1 text-decoration-none {{ $giorno->isSameDay($oggi) ? 'bg-primary text-white rounded-circle' : ($fuoriMese ? 'text-muted' : 'text-body') }}"
                                    style="width: 1.75rem; height: 1.75rem; font-size: 0.875rem;"
                                >
                                    {{ $giorno->day }}
                                </a>

                                <div class="d-flex flex-column gap-1">
                                    @foreach ($daMostrare as $appuntamento)
                                        <a
                                            href="{{ route('calendario.giorno', ['data' => $giorno->format('Y-m-d')]) }}"
                                            class="d-block text-truncate text-decoration-none text-white rounded px-1"
                                            style="font-size: 0.7rem; background-color: {{ $appuntamento->operatoreAppuntamento->colore ?? $appuntamento->tipoAppuntamento->colore ?? '#adb5bd' }};"
                                            title="{{ \Illuminate\Support\Carbon::parse($appuntamento->ora_inizio)->format('H:i') }} {{ $appuntamento->titolo }}"
                                        >
                                            {{ \Illuminate\Support\Carbon::parse($appuntamento->ora_inizio)->format('H:i') }} {{ $appuntamento->titolo }}
                                        </a>
                                    @endforeach

                                    @if ($altri > 0)
                                        <a href="{{ route('calendario.giorno', ['data' => $giorno->format('Y-m-d')]) }}" class="small text-muted text-decoration-none">
                                            +{{ $altri }} altri
                                        </a>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
