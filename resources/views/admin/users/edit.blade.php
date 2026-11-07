@extends('layouts.admin')

@section('title', 'Edit Pengguna - RAPI Sekolah')

@section('page_title', 'Edit Pengguna')

@section('page_subtitle', 'Perbarui informasi akun pengguna')

@section('content')

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-person-gear"></i>
                Edit Pengguna
            </h4>

            <p class="text-muted mb-0">
                Perbarui data pengguna sistem RAPI Sekolah.
            </p>
        </div>

        
    </div>

    {{-- Pesan Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit Pengguna --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-pencil-square"></i>
                Formulir Pengguna
            </h6>
        </div>

        <div class="card-body p-4">

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Username --}}
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $user->username) }}"
                        required
                    >

                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nama Lengkap --}}
                <div class="mb-3">
                    <label for="fullname" class="form-label fw-semibold">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="fullname"
                        name="fullname"
                        class="form-control @error('fullname') is-invalid @enderror"
                        value="{{ old('fullname', $user->fullname) }}"
                        required
                    >

                    @error('fullname')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nomor Telepon --}}
                <div class="mb-3">
                    <label for="phone_number" class="form-label fw-semibold">
                        No. Telepon
                    </label>

                    <input
                        type="text"
                        id="phone_number"
                        name="phone_number"
                        class="form-control @error('phone_number') is-invalid @enderror"
                        value="{{ old('phone_number', $user->phone_number) }}"
                        required
                    >

                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label for="role_id" class="form-label fw-semibold">
                        Role Pengguna
                    </label>

                    <select
                        id="role_id"
                        name="role_id"
                        class="form-select @error('role_id') is-invalid @enderror"
                        required
                    >
                        @foreach ($roles as $role)
                            <option
                                value="{{ $role->id }}"
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}
                            >
                                {{ $role->role_name }}
                            </option>
                        @endforeach
                    </select>

                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                

                {{-- Tombol --}}
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('users.index') }}" class="btn btn-light border">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

@endsection