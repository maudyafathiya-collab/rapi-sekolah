
@php
    $layout = 'layouts.public';

    if (Auth::check()) {
        $layout = match ((int) Auth::user()->role_id) {
            1 => 'layouts.admin',
            2 => 'layouts.waka',
            3, 4 => 'layouts.guru',
            5 => 'layouts.pic',
            default => 'layouts.public',
        };
    }
@endphp

@extends($layout)

@section('title', 'Pengaturan Sesi | RAPI Sekolah')
@section('page_title', 'Pengaturan Sesi')
@section('page_subtitle', 'Kelola jadwal sesi peminjaman ruangan')

@section('content')

    <div class="mb-4">
        <h4 class="fw-bold mb-1">Pengaturan Sesi Peminjaman</h4>
        <p class="text-muted mb-0">
            Atur jadwal sesi untuk hari sekolah dan hari libur.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM TAMBAH SESI --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">Tambah Sesi Baru</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('booking-sessions.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nama Sesi</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Contoh: Sesi 1"
                               value="{{ old('name') }}"
                               required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Jenis Hari</label>
                        <select name="day_type" class="form-select" required>
                            <option value="">Pilih jenis hari</option>
                            <option value="sekolah" {{ old('day_type') == 'sekolah' ? 'selected' : '' }}>
                                Hari Sekolah
                            </option>
                            <option value="libur" {{ old('day_type') == 'libur' ? 'selected' : '' }}>
                                Hari Libur
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time"
                               name="start_time"
                               class="form-control"
                               value="{{ old('start_time') }}"
                               required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time"
                               name="end_time"
                               class="form-control"
                               value="{{ old('end_time') }}"
                               required>
                    </div>

                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            + Tambah
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DAFTAR SESI --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="fw-bold mb-0">Daftar Jadwal Sesi</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Sesi</th>
                            <th>Jenis Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $session->name }}</td>
                                <td>
                                    @if($session->day_type == 'sekolah')
                                        <span class="badge bg-primary">Hari Sekolah</span>
                                    @else
                                        <span class="badge bg-info text-dark">Hari Libur</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</td>
                                <td>
                                    @if($session->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <details>
                                        <summary class="btn btn-warning btn-sm">
                                            Edit
                                        </summary>

                                        <form action="{{ route('booking-sessions.update', $session->id) }}"
                                              method="POST"
                                              class="mt-2"
                                              style="min-width: 220px;">
                                            @csrf
                                            @method('PUT')

                                            <input type="text"
                                                   name="name"
                                                   class="form-control form-control-sm mb-2"
                                                   value="{{ $session->name }}"
                                                   required>

                                            <select name="day_type"
                                                    class="form-select form-select-sm mb-2"
                                                    required>
                                                <option value="sekolah" {{ $session->day_type == 'sekolah' ? 'selected' : '' }}>
                                                    Hari Sekolah
                                                </option>
                                                <option value="libur" {{ $session->day_type == 'libur' ? 'selected' : '' }}>
                                                    Hari Libur
                                                </option>
                                            </select>

                                            <input type="time"
                                                   name="start_time"
                                                   class="form-control form-control-sm mb-2"
                                                   value="{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}"
                                                   required>

                                            <input type="time"
                                                   name="end_time"
                                                   class="form-control form-control-sm mb-2"
                                                   value="{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}"
                                                   required>

                                            <button type="submit" class="btn btn-primary btn-sm">
                                                Simpan Perubahan
                                            </button>
                                        </form>
                                    </details>

                                    <form action="{{ route('booking-sessions.toggle', $session->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-secondary btn-sm">
                                            {{ $session->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('booking-sessions.destroy', $session->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus sesi ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada jadwal sesi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
