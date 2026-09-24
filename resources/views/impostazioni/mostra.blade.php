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

                <hr class="my-4">

                <h2 class="h6">Webhook (messaggi in arrivo)</h2>
                <p class="text-muted small">
                    Da inserire in Meta for Developers → WhatsApp → Configurazione → Webhook.
                </p>

                <div class="mb-3">
                    <label class="form-label">URL di callback</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ url('/webhook/whatsapp') }}" readonly>
                    </div>
                    <div class="form-text">
                        Deve essere un indirizzo raggiungibile da internet (HTTPS) — in locale serve un tunnel come ngrok.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="whatsapp_webhook_verify_token" class="form-label">Verifica token</label>
                    <div class="input-group">
                        <input
                            type="text"
                            name="whatsapp_webhook_verify_token"
                            id="whatsapp_webhook_verify_token"
                            class="form-control"
                            value="{{ old('whatsapp_webhook_verify_token', $impostazioni->whatsapp_webhook_verify_token) }}"
                        >
                        <button type="button" class="btn btn-outline-secondary" onclick="generaTokenWebhook()">Genera</button>
                    </div>
                    <div class="form-text">Deve essere identico qui e nel campo "Verifica il token" su Meta.</div>
                </div>

                <button type="submit" class="btn btn-primary">Salva</button>
            </form>
        </div>
    </div>

    <script>
        function generaTokenWebhook() {
            const array = new Uint8Array(24);
            crypto.getRandomValues(array);
            const token = Array.from(array, (b) => b.toString(16).padStart(2, '0')).join('');
            document.getElementById('whatsapp_webhook_verify_token').value = token;
        }
    </script>
@endsection
