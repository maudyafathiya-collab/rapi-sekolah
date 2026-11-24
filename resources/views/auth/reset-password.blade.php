
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - RAPI Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-sm border-0" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">

            <h4 class="text-center fw-bold mb-2">Buat Password Baru</h4>
            <p class="text-muted text-center small mb-4">
                Silakan masukkan password baru untuk akun RAPI kamu.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email', $email) }}"
                        required
                        readonly
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Minimal 8 karakter"
                        minlength="8"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Password Baru
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        placeholder="Ulangi password baru"
                        minlength="8"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">
                    Simpan Password Baru
                </button>
            </form>

        </div>
    </div>

</body>
</html>
