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

@section('title', 'Kelola Ruangan | RAPI Sekolah')
@section('page_title', 'Kelola Ruangan')
@section('page_subtitle', 'Daftar ruangan sekolah')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Kelola Ruangan</h4>

        <a href="{{ route('rooms.create') }}" class="btn btn-primary">
            + Tambah Ruangan
        </a>
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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Ruangan</th>
                            <th>Kapasitas</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $room->room_name }}</td>
                                <td>{{ $room->capacity }}</td>
                                <td>{{ $room->location }}</td>
                                <td>
                                    @if($room->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('rooms.edit', $room->id) }}"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('rooms.destroy', $room->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
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
                                    Belum ada data ruangan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection