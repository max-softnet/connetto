<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Accedi — Connetto</title>

        @include('partials.favicon')

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <div class="d-flex flex-column align-items-center px-3 pt-5" style="min-height: 100vh;">
            <img src="{{ asset('images/logo-login.png') }}" alt="Connetto" class="mb-4" style="width: 200px; max-width: 80%;">

            <div class="card shadow" style="width: 100%; max-width: 360px;">
                <div class="card-body p-4">
                    <h2 class="h5 d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-person-circle"></i>
                        Accedi
                    </h2>
                    <hr>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small">
                            @foreach ($errors->all() as $errore)
                                {{ $errore }}
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label text-uppercase small fw-semibold text-muted">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                required
                                autofocus
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-uppercase small fw-semibold text-muted">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="ricordami" id="ricordami" class="form-check-input">
                            <label for="ricordami" class="form-check-label small">Resta connesso</label>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 text-white">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Accedi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
