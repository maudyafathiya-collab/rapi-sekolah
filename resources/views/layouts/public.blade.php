```blade
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

    @stack('styles')
</head>

<body class="public-body">

    <!-- Navbar Publik -->
    <nav class="navbar navbar-expand-lg public-navbar sticky-top">
        <div class="container py-2">

            <a class="navbar-brand public-brand d-flex align-items-center gap-3"
               href="{{ url('/') }}">

                <img src="{{ asset('images/logo-smkn1-bantul.png') }}"
                     alt="Logo SMKN 1 Bantul"
                     class="public-logo">

                <div class="d-flex flex-column">
                    <span class="brand-title">RAPI Sekolah</span>
                    <small class="brand-subtitle">SMK Negeri 1 Bantul</small>
                </div>
            </a>

            <div class="ms-auto">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-school">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-school">
                        Login
                    </a>
                @endauth
            </div>

        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="public-main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="public-footer">
        <div class="container text-center">
            <p class="mb-1">
                &copy; {{ date('Y') }} RAPI Sekolah
            </p>
            <small>
                Sistem Informasi Manajemen Sarana dan Prasarana
                <br>
                SMK Negeri 1 Bantul
            </small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
```