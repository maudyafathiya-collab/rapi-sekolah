
@extends('layouts.pic')

@section('title', 'Daftar Ruangan')

@section('content')
<div class="container-fluid">

    {{-- Header halaman --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Daftar Ruangan</h4>
        <p class="text-muted mb-0">
            Berikut daftar ruangan yang menjadi tanggung jawabmu.
        </p>
    </div>

    {{-- Informasi jumlah ruangan --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                <i class="bi bi-door-open fs-3"></i>
            </div>
            <div>
                <h6 class="text-muted mb-1">Total Ruangan yang Diampu</h6>
                <h3 class="fw-bold mb-0">{{ $rooms->count() }}</h3>
            </div>
        </div>
    </div>

    {{-- Tabel daftar ruangan --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Ruangan Tanggung Jawab Saya</h5>
        </div>

        <div class="card-body">
            @if($rooms->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-door-closed text-muted fs-1"></i>
                    <h6 class="fw-bold mt-3">Belum Ada Ruangan</h6>
                    <p class="text-muted mb-0">
                        Saat ini belum ada ruangan yang ditugaskan kepada akunmu.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="60">No</th>
                                <th>Nama Ruangan</th>
                                <th>Kapasitas</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">
                                        {{ $room->room_name }}
                                    </td>
                                    <td>{{ $room->capacity }} orang</td>
                                    <td>{{ $room->location }}</td>
                                    <td>
                                        @if($room->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
