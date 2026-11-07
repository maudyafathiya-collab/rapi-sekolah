@extends('layouts.admin')

@section('title', 'Kelola Pengguna - RAPI Sekolah')

@section('page_title', 'Kelola Pengguna')

@section('page_subtitle', 'Mengelola data pengguna sistem RAPI')

@section('content')

    {{-- Header Halaman --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-people"></i>
                Kelola Pengguna
            </h4>

            <p class="text-muted mb-0">
                Mengelola akun pengguna sistem RAPI Sekolah.
            </p>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Tambah Pengguna
        </a>
    </div>

    {{-- Pesan Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pesan Error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i>
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pesan Validasi --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabel Pengguna --}}
    <div class="card">

        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-table"></i>
                Daftar Pengguna
            </h6>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>No. Telepon</th>
                            <th>Role</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($users as $user)

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $user->username }}</td>

                                <td>{{ $user->fullname }}</td>

                                <td>{{ $user->phone_number }}</td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $user->role->role_name ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('users.edit', $user->id) }}"
                                       class="btn btn-sm btn-warning"
                                       title="Edit pengguna">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('users.destroy', $user->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Hapus pengguna">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-person-x fs-2 d-block mb-2"></i>

                                    Belum ada data pengguna.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection