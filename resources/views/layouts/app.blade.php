<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Connetto')</title>

        @include('partials.favicon')

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light d-flex flex-column" style="min-height: 100vh;">
        <nav class="navbar navbar-dark bg-brand px-2 px-md-3">
            <div class="d-flex align-items-center gap-2">
                <button
                    class="btn btn-link text-white d-md-none p-0"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#sidebar"
                    aria-controls="sidebar"
                    aria-label="Apri menu"
                >
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a class="navbar-brand mb-0" href="{{ route('home') }}">Connetto</a>
            </div>
        </nav>

        <div class="d-flex align-items-stretch flex-grow-1">
            @php
                $utenteCorrente = auth()->user();

                $vociMenu = [
                    ['route' => 'home', 'label' => 'Home', 'icon' => 'bi-house', 'active' => request()->routeIs('home')],
                    ['route' => 'calendario.giorno', 'label' => 'Calendario', 'icon' => 'bi-calendar3', 'active' => request()->routeIs('calendario.*')],
                ];

                if ($utenteCorrente->isAdmin()) {
                    $vociMenu[] = ['route' => 'modelli-messaggio.index', 'label' => 'Modelli messaggio', 'icon' => 'bi-chat-left-text', 'active' => request()->routeIs('modelli-messaggio.*')];
                    $vociMenu[] = ['route' => 'messaggi.index', 'label' => 'Messaggi', 'icon' => 'bi-envelope', 'active' => request()->routeIs('messaggi.*')];
                    $vociMenu[] = ['route' => 'log-whatsapp.index', 'label' => 'Log WhatsApp', 'icon' => 'bi-whatsapp', 'active' => request()->routeIs('log-whatsapp.*')];
                    $vociMenu[] = ['route' => 'automazioni.index', 'label' => 'Automazioni', 'icon' => 'bi-lightning-charge', 'active' => request()->routeIs('automazioni.*')];
                    $vociMenu[] = ['route' => 'utenti.index', 'label' => 'Utenti', 'icon' => 'bi-people', 'active' => request()->routeIs('utenti.*')];
                    $vociMenu[] = ['route' => 'impostazioni.mostra', 'label' => 'Impostazioni', 'icon' => 'bi-gear', 'active' => request()->routeIs('impostazioni.*')];
                }
            @endphp

            <div class="offcanvas offcanvas-start bg-dark text-white d-flex flex-column" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel" style="width: 240px;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title text-white" id="sidebarLabel">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Chiudi"></button>
                </div>
                <div class="offcanvas-body d-md-flex flex-column p-2">
                    <div class="text-center py-3 mb-1">
                        <img src="{{ asset('images/logo-sidebar-scuro.png') }}" alt="Connetto" style="width: 150px; max-width: 100%;">
                    </div>

                    <ul class="nav nav-pills flex-column">
                        @foreach ($vociMenu as $voce)
                            <li class="nav-item mb-1">
                                <a
                                    href="{{ route($voce['route']) }}"
                                    class="nav-link text-white d-flex align-items-center gap-2 {{ $voce['active'] ? 'active' : '' }}"
                                >
                                    <i class="bi {{ $voce['icon'] }}"></i>
                                    {{ $voce['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-auto pt-3 border-top border-secondary-subtle">
                        <div class="d-flex align-items-center gap-2 px-2 mb-2">
                            <i class="bi bi-person-circle fs-4"></i>
                            <div class="small">
                                <div>{{ $utenteCorrente->name }}</div>
                                <div class="text-white-50">{{ ucfirst($utenteCorrente->ruolo) }}</div>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                                <i class="bi bi-box-arrow-right"></i>
                                Esci
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1 p-3 p-md-4" style="min-width: 0;">
                @if (session('successo'))
                    <div class="alert alert-success">{{ session('successo') }}</div>
                @endif

                @if (session('errore_invio'))
                    <div class="alert alert-danger">{{ session('errore_invio') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $errore)
                                <li>{{ $errore }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </body>
</html>
