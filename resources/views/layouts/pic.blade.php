
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
    <aside class="sidebar" id="picSidebar">

        <div class="sidebar-brand">
    <img src="{{ asset('images/logo-smkn1-bantul.png') }}"
         alt="Logo SMKN 1 Bantul"
         class="school-logo">

    <span>RAPI Sekolah</span>
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

                <a href="{{ route('pic.rooms.index') }}"
                class="nav-link {{ request()->routeIs('pic.rooms.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i>
                Daftar Ruangan
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

    {{-- OVERLAY SIDEBAR MOBILE --}}
    <div class="sidebar-overlay" id="picOverlay"></div>

    {{-- KONTEN UTAMA --}}
    <main class="main-content">

        {{-- TOPBAR --}}
        <header class="topbar">

            <div class="d-flex align-items-center gap-3">

                <button
                    type="button"
                    class="sidebar-toggle"
                    id="picToggle"
                    aria-label="Buka menu"
                >
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h5 class="mb-0">
                        @yield('page_title', 'Dashboard PIC')
                    </h5>

                    <small class="text-muted">
                        @yield('page_subtitle', 'Panel pengelolaan sarana dan prasarana')
                    </small>
                </div>

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

{{-- JAVASCRIPT SIDEBAR MOBILE --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('picSidebar');
    const toggle = document.getElementById('picToggle');
    const overlay = document.getElementById('picOverlay');

    if (!sidebar || !toggle || !overlay) return;

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    toggle.addEventListener('click', function () {
        if (sidebar.classList.contains('show')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    sidebar.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', closeSidebar);
    });

});
</script>

@stack('scripts')

</body>
</html>
