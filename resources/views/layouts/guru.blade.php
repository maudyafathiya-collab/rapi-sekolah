
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RAPI Sekolah')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @stack('styles')
</head>
<body>

<div class="app-wrapper">

    {{-- SIDEBAR GURU DAN ORGANISASI --}}
    <aside class="sidebar shadow-sm" id="guruSidebar">

        <div class="sidebar-brand">
    <img src="{{ asset('images/logo-smkn1-bantul.png') }}"
         alt="Logo SMKN 1 Bantul"
         class="school-logo">

    <span>RAPI Sekolah</span>
</div>

        <nav class="sidebar-menu">

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <a href="{{ route('calendar') }}"
               class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i> Kalender Agenda
            </a>

            <a href="{{ route('events.index') }}"
               class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> Pengajuan Agenda
            </a>

            <a href="{{ route('complaints.index') }}"
               class="nav-link {{ request()->routeIs('complaints.index', 'complaints.create', 'complaints.edit') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> Pengaduan
            </a>

            <a href="{{ route('complaints.history') }}"
               class="nav-link {{ request()->routeIs('complaints.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat Pengaduan
            </a>

        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- OVERLAY SIDEBAR MOBILE --}}
    <div class="sidebar-overlay" id="guruOverlay"></div>

    {{-- KONTEN UTAMA --}}
    <div class="main-content">

        {{-- TOPBAR --}}
        <header class="topbar shadow-sm">

            <div class="d-flex align-items-center gap-3">

                <button type="button"
                        class="sidebar-toggle"
                        id="guruToggle"
                        aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h5 class="mb-0 fw-semibold fs-6">
                        @yield('page_title', 'Halaman Guru')
                    </h5>

                    <small class="text-muted">
                        @yield('page_subtitle', 'Panel Guru dan Organisasi')
                    </small>
                </div>

            </div>

            <div class="text-end">
                <div class="fw-medium small">
                    {{ Auth::user()->fullname ?? 'Pengguna' }}
                </div>

                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    {{ Auth::user()->role->role_name ?? 'Guru' }}
                </span>
            </div>

        </header>

        {{-- KONTEN SETIAP HALAMAN --}}
        <main class="p-4">
            @yield('content')
        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- JAVASCRIPT SIDEBAR MOBILE --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('guruSidebar');
    const toggle = document.getElementById('guruToggle');
    const overlay = document.getElementById('guruOverlay');

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
