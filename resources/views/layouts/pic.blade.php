
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard PIC | RAPI Sekolah')</title>

    {{-- CSS utama --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    @stack('styles')
</head>

<body>

<div class="app-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="sidebar-brand">
            <i class="bi bi-tools"></i>
            RAPI Sekolah
        </div>

        <nav class="sidebar-menu">

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>

            <a href="{{ route('calendar') }}"
               class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                Kalender Agenda
            </a>

            <a href="{{ route('complaints.index') }}"
               class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                Pengaduan Saya
            </a>

        </nav>

        <div class="sidebar-footer">

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </button>
            </form>

        </div>

    </aside>

    {{-- KONTEN UTAMA --}}
    <main class="main-content">

        {{-- TOPBAR --}}
        <header class="topbar">

            <div>
                <h5 class="mb-0">@yield('page_title', 'Dashboard PIC')</h5>

                <small class="text-muted">
                    @yield('page_subtitle', 'Panel pengelolaan sarana dan prasarana')
                </small>
            </div>

            <div class="text-end">
                <span class="fw-semibold">
                    {{ Auth::user()->fullname }}
                </span>

                <small class="text-muted d-block">PIC</small>
            </div>

        </header>

        {{-- ISI HALAMAN --}}
        <div class="container-fluid p-4">

            @yield('content')

        </div>

    </main>

</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Script tambahan dari halaman --}}

@stack('scripts')

</body>
</html>
