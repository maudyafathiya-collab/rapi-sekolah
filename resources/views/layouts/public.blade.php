<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RAPI Sekolah')</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS Eksternal -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        /* Efek geser halus (smooth scroll) ala landing page */
        html {
            scroll-behavior: smooth;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-light">

    <!-- Navbar Publik -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container py-2">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="bi bi-building-fill-gear fs-4"></i>
                RAPI Sekolah
            </a>
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm px-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-4">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer Sederhana -->
    <footer class="text-center py-4 text-muted small border-top bg-white mt-5">
        <div class="container">
            &copy; {{ date('Y') }} RAPI Sekolah. Sistem Informasi Manajemen Sarana Prasarana.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>