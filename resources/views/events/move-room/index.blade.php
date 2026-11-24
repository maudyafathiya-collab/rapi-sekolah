
@php
    $role = Auth::check() ? (int) Auth::user()->role_id : null;

    $layout = match ($role) {
        1 => 'layouts.admin',
        2 => 'layouts.waka',
        3, 4 => 'layouts.guru',
        5 => 'layouts.pic',
        default => 'layouts.public',
    };
@endphp

@extends($layout)

@section('title', 'Pindahkan Ruangan | RAPI Sekolah')
@section('page_title', 'Pindahkan Ruangan')
@section('page_subtitle', 'Kelola pemindahan ruangan untuk agenda yang telah disetujui')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Agenda yang Dapat Dipindahkan</h4>
        <p class="text-muted small mb-0">
            Hanya agenda yang sudah disetujui dan belum dimulai yang ditampilkan.
        </p>
    </div>
</div>

{{-- Pesan berhasil --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Pesan error --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>Nama Kegiatan</th>
                        <th>Pemohon</th>
                        <th>Ruangan Saat Ini</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($events as $index => $event)
                        <tr>
                            <td class="ps-3">
                                {{ $index + 1 }}
                            </td>

                            <td class="fw-medium">
                                {{ $event->event_name }}
                            </td>

                            <td>
                                {{ $event->reporter->fullname ?? '-' }}
                            </td>

                            <td>
                                {{ $event->room->room_name ?? '-' }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                            </td>

                            <td>
                                {{ substr($event->start_time, 0, 5) }}
                                -
                                {{ substr($event->end_time, 0, 5) }}
                            </td>

                            <td class="text-end pe-3">
                                <a href="{{ route('events.move-room.edit', $event->id) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-arrow-left-right me-1"></i>
                                    Pindahkan
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Tidak ada agenda yang dapat dipindahkan saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

@endsection
