@extends('layouts.app')

@section('title', 'Impostazioni — Connetto')

@section('content')
    <x-page-header icon="bi-gear" title="Impostazioni" subtitle="Configurazione dei servizi di messaggistica" />

    <div class="card shadow-sm" style="max-width: 560px;">
        <div class="card-body">
            <h2 class="h6 d-flex align-items-center gap-2">
                <i class="bi bi-whatsapp text-success"></i>
                WhatsApp (Meta Cloud API)
            </h2>
            <p class="text-muted small">
                Credenziali dell'app WhatsApp Business creata su
                <a href="https://developers.facebook.com" target="_blank" rel="noopener">Meta for Developers</a>.
            </p>

            <form action="{{ route('impostazioni.salva') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="whatsapp_phone_number_id" class="form-label">Phone Number ID</label>
                    <input
                        type="text"
                        name="whatsapp_phone_number_id"
                        id="whatsapp_phone_number_id"
                        class="form-control"
                        value="{{ old('whatsapp_phone_number_id', $impostazioni->whatsapp_phone_number_id) }}"
                        placeholder="es. 123456789012345"
                    >
                    <div class="form-text">Lo trovi in Meta for Developers → la tua app → WhatsApp → Configurazione API.</div>
                </div>

                <div class="mb-3">
                    <label for="whatsapp_token" class="form-label">
                        Access Token
                        @if ($impostazioni->whatsapp_token)
                            <span class="badge text-bg-success">configurato</span>
                        @endif
                    </label>
                    <input
                        type="password"
                        name="whatsapp_token"
                        id="whatsapp_token"
                        class="form-control"
                        placeholder="{{ $impostazioni->whatsapp_token ? '•••••••••••••••• (lascia vuoto per non modificare)' : 'Incolla qui il token' }}"
                        autocomplete="off"
                    >
                    <div class="form-text">Per uso continuativo serve un token permanente da un System User (non quello temporaneo di 24h di prova).</div>
                </div>

                <button type="submit" class="btn btn-primary">Salva</button>
            </form>
        </div>
    </div>
@endsection
