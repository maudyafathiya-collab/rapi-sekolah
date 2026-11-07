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

@section('title', 'History Pengaduan | RAPI Sekolah')
@section('page_title', 'History Pengaduan')
@section('page_subtitle', 'Arsip riwayat laporan dan penanganan pengaduan fasilitas')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Riwayat Pengaduan</h4>
            <p class="text-muted small mb-0">Daftar arsip pengaduan fasilitas yang telah diproses atau selesai.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Ruangan</th>
                            <th>Pelapor</th>
                            <th>Deskripsi</th>
                            <th>PIC</th>
                            <th>Status</th>
                            <th class="pe-3">Tanggal Pengaduan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($complaints as $complaint)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $complaint->room->room_name ?? '-' }}</td>
                                <td>{{ $complaint->reporter->fullname ?? '-' }}</td>
                                <td>{{ Str::limit($complaint->description, 35) }}</td>
                                <td>{{ $complaint->handler->fullname ?? '-' }}</td>
                                <td>
                                    @php
                                        $status = strtolower($complaint->status_repair ?? '');
                                    @endphp
                                    @if(str_contains($status, 'selesai'))
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $complaint->status_repair }}</span>
                                    @endif
                                </td>
                                <td class="pe-3">
                                    {{ $complaint->created_at ? $complaint->created_at->format('d M Y H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                                    Belum ada pengaduan yang selesai atau masuk dalam riwayat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection