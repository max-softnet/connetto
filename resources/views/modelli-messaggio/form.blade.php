@extends('layouts.app')

@section('title', ($modello->exists ? 'Modifica' : 'Nuovo') . ' modello — Connetto')

@section('content')
    <x-page-header icon="bi-chat-left-text" :title="$modello->exists ? 'Modifica modello' : 'Nuovo modello'" />

    <form action="{{ $modello->exists ? route('modelli-messaggio.update', $modello) : route('modelli-messaggio.store') }}" method="POST">
        @csrf
        @if ($modello->exists)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $modello->nome) }}" required>
        </div>

        <div class="mb-3">
            <label for="canale" class="form-label">Canale</label>
            <select name="canale" id="canale" class="form-select" required onchange="aggiornaCampiCanale()">
                <option value="email" {{ old('canale', $modello->canale) === 'email' ? 'selected' : '' }}>Email</option>
                <option value="sms" {{ old('canale', $modello->canale) === 'sms' ? 'selected' : '' }}>SMS</option>
                <option value="whatsapp" {{ old('canale', $modello->canale) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
            </select>
        </div>

        <div class="mb-3" id="campo-oggetto">
            <label for="oggetto" class="form-label">Oggetto <span class="text-muted small">(solo email)</span></label>
            <input type="text" name="oggetto" id="oggetto" class="form-control" value="{{ old('oggetto', $modello->oggetto) }}">
        </div>

        <div class="card bg-light border-success-subtle mb-3" id="campo-template-whatsapp">
            <div class="card-body">
                <h2 class="h6 d-flex align-items-center gap-2">
                    <i class="bi bi-whatsapp text-success"></i>
                    Template WhatsApp approvato <span class="text-muted small fw-normal">(opzionale)</span>
                </h2>
                <p class="text-muted small">
                    Se compili questi campi, l'invio userà il template approvato su Meta invece del testo libero
                    (obbligatorio per contattare un cliente che non ti ha scritto nelle ultime 24 ore). I segnaposto
                    nel testo qui sotto verranno mandati come parametri <code>{{1}}</code>, <code>{{2}}</code>, ...
                    nell'ordine in cui compaiono.
                </p>

                <div class="row g-3">
                    <div class="col-sm-8">
                        <label for="whatsapp_template_nome" class="form-label">Nome template</label>
                        <input
                            type="text"
                            name="whatsapp_template_nome"
                            id="whatsapp_template_nome"
                            class="form-control"
                            value="{{ old('whatsapp_template_nome', $modello->whatsapp_template_nome) }}"
                            placeholder="es. promemoria_appuntamento"
                        >
                    </div>
                    <div class="col-sm-4">
                        <label for="whatsapp_template_lingua" class="form-label">Lingua</label>
                        <input
                            type="text"
                            name="whatsapp_template_lingua"
                            id="whatsapp_template_lingua"
                            class="form-control"
                            value="{{ old('whatsapp_template_lingua', $modello->whatsapp_template_lingua) }}"
                            placeholder="es. it"
                        >
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-sm-6">
                        <label for="whatsapp_formato_parametri" class="form-label">Formato variabili del template</label>
                        <select name="whatsapp_formato_parametri" id="whatsapp_formato_parametri" class="form-select" onchange="aggiornaCampoNomeParametro()">
                            <option value="posizionale" {{ old('whatsapp_formato_parametri', $modello->whatsapp_formato_parametri) === 'posizionale' ? 'selected' : '' }}>
                                Numerate — @{{1}}, @{{2}}, ...
                            </option>
                            <option value="nominale" {{ old('whatsapp_formato_parametri', $modello->whatsapp_formato_parametri) === 'nominale' ? 'selected' : '' }}>
                                Con nome — @{{nome}}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-6" id="campo-nome-parametro">
                        <label for="whatsapp_nome_parametro" class="form-label">
                            Nome della variabile <span class="text-muted small fw-normal">(solo se diverso dai segnaposto sotto)</span>
                        </label>
                        <input
                            type="text"
                            name="whatsapp_nome_parametro"
                            id="whatsapp_nome_parametro"
                            class="form-control"
                            value="{{ old('whatsapp_nome_parametro', $modello->whatsapp_nome_parametro) }}"
                            placeholder="lascia vuoto se coincide, es. nome"
                        >
                        <div class="form-check mt-2">
                            <input
                                type="checkbox"
                                name="whatsapp_header_parametro"
                                id="whatsapp_header_parametro"
                                class="form-check-input"
                                value="1"
                                {{ old('whatsapp_header_parametro', $modello->whatsapp_header_parametro) ? 'checked' : '' }}
                            >
                            <label for="whatsapp_header_parametro" class="form-check-label small">
                                Le variabili compaiono anche nell'header del template
                            </label>
                        </div>
                    </div>
                </div>

                <p class="text-muted small mt-2 mb-0">
                    Con il formato "con nome": se il template usa variabili con lo <strong>stesso nome</strong> dei nostri
                    segnaposto (es. <code>@{{titolo}}</code>, <code>@{{data}}</code>, <code>@{{ora_inizio}}</code>), lascia
                    vuoto il campo "Nome della variabile" — ogni segnaposto del testo diventa il parametro corrispondente.
                    Se invece il template ha <strong>un solo</strong> parametro con un nome diverso (es. <code>nome</code>),
                    indicalo lì: il valore dell'unico segnaposto nel testo verrà usato per riempirlo.
                </p>
            </div>
        </div>

        <div class="mb-3">
            <label for="corpo" class="form-label">Testo</label>
            <textarea name="corpo" id="corpo" class="form-control" rows="6" required>{{ old('corpo', $modello->corpo) }}</textarea>
            <div class="form-text">
                Segnaposto disponibili: <code>@{{titolo}}</code>, <code>@{{data}}</code>,
                <code>@{{ora_inizio}}</code>, <code>@{{ora_fine}}</code>, <code>@{{tipo}}</code>
            </div>
        </div>

        <div class="mb-4">
            <label for="tipo_appuntamento" class="form-label">Tipo appuntamento <span class="text-muted small">(opzionale)</span></label>
            <select name="tipo_appuntamento" id="tipo_appuntamento" class="form-select">
                <option value="">— Qualsiasi tipo —</option>
                @foreach ($tipiAppuntamento as $tipo)
                    <option value="{{ $tipo->nome }}" {{ old('tipo_appuntamento', $modello->tipo_appuntamento) === $tipo->nome ? 'selected' : '' }}>
                        {{ $tipo->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="{{ route('modelli-messaggio.index') }}" class="btn btn-link">Annulla</a>
    </form>

    <script>
        function aggiornaCampiCanale() {
            const canale = document.getElementById('canale').value;
            document.getElementById('campo-oggetto').style.display = canale === 'email' ? '' : 'none';
            document.getElementById('campo-template-whatsapp').style.display = canale === 'whatsapp' ? '' : 'none';
        }

        function aggiornaCampoNomeParametro() {
            const formato = document.getElementById('whatsapp_formato_parametri').value;
            document.getElementById('campo-nome-parametro').style.display = formato === 'nominale' ? '' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            aggiornaCampiCanale();
            aggiornaCampoNomeParametro();
        });
    </script>
@endsection
