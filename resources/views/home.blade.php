@extends('layouts.app')

@section('title', 'Connetto')

@section('content')
    <x-page-header icon="bi-speedometer2" title="Dashboard" subtitle="Riepilogo delle sezioni principali" />

    <div class="row g-3">
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('calendario.giorno') }}" class="card text-decoration-none text-body shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0 bg-primary text-white" style="width: 56px; height: 56px;">
                        <i class="bi bi-calendar3 fs-4"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted">Calendario</div>
                        <div class="fs-3 fw-bold lh-1">{{ $appuntamentiOggi }}</div>
                        <div class="small text-muted">appuntamenti oggi</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('modelli-messaggio.index') }}" class="card text-decoration-none text-body shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0 bg-warning text-dark" style="width: 56px; height: 56px;">
                        <i class="bi bi-chat-left-text fs-4"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted">Modelli messaggio</div>
                        <div class="fs-3 fw-bold lh-1">{{ $modelliMessaggio }}</div>
                        <div class="small text-muted">modelli disponibili</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('messaggi.index') }}" class="card text-decoration-none text-body shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0 bg-success text-white" style="width: 56px; height: 56px;">
                        <i class="bi bi-envelope fs-4"></i>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted">Messaggi</div>
                        <div class="fs-3 fw-bold lh-1">{{ $messaggiInBozza }}</div>
                        <div class="small text-muted">in bozza</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
