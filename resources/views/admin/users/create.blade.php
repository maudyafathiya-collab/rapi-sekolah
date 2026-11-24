@extends('layouts.admin')

@section('title', 'Tambah Pengguna - RAPI Sekolah')
@section('page_title', 'Tambah Pengguna')
@section('page_subtitle', 'Tambahkan akun pengguna baru')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tambah Pengguna</h4>
        <p class="text-muted mb-0">Isi formulir berikut untuk menambahkan akun baru.</p>
    </div>

    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali
    </a>
</div>

{{-- Pesan validasi --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Gagal menyimpan pengguna!</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-person-plus me-2"></i>
            Formulir Pengguna Baru
        </h6>
    </div>

    <div class="card-body p-4">

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            {{-- Username --}}
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">
                    Username <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control @error('username') is-invalid @enderror"
                    value="{{ old('username') }}"
                    placeholder="Masukkan username"
                    required
                >

                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nama Lengkap --}}
            <div class="mb-3">
                <label for="fullname" class="form-label fw-semibold">
                    Nama Lengkap <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    id="fullname"
                    name="fullname"
                    class="form-control @error('fullname') is-invalid @enderror"
                    value="{{ old('fullname') }}"
                    placeholder="Masukkan nama lengkap"
                    required
                >

                @error('fullname')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
<div class="mb-3">
    <label for="email" class="form-label fw-semibold">
        Email
    </label>

    <input
        type="email"
        id="email"
        name="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email') }}"
        placeholder="Masukkan alamat email"
    >

    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <small class="text-muted">
        Email digunakan untuk pemulihan password jika pengguna lupa password.
    </small>
</div>

            {{-- Nomor Telepon --}}
            <div class="mb-3">
                <label for="phone_number" class="form-label fw-semibold">
                    No. Telepon <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    id="phone_number"
                    name="phone_number"
                    class="form-control @error('phone_number') is-invalid @enderror"
                    value="{{ old('phone_number') }}"
                    placeholder="Masukkan nomor telepon"
                    required
                >

                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Role --}}
            <div class="mb-3">
                <label for="role_id" class="form-label fw-semibold">
                    Role <span class="text-danger">*</span>
                </label>

                <select
                    id="role_id"
                    name="role_id"
                    class="form-select @error('role_id') is-invalid @enderror"
                    required
                >
                    <option value="">-- Pilih Role --</option>

                    @foreach ($roles as $role)
                        <option
                            value="{{ $role->id }}"
                            {{ old('role_id') == $role->id ? 'selected' : '' }}
                        >
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>

                @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">
                    Password <span class="text-danger">*</span>
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan password"
                    required
                >

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <small class="text-muted">
                    Password akan digunakan pengguna untuk login ke sistem RAPI.
                </small>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-light border">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Simpan Pengguna
                </button>
            </div>

        </form>

    </div>
</div>

@endsection