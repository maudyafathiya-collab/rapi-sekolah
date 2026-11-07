
@php
    $layout = 'layouts.public';
    $isAdminOrWaka = false;

    if (Auth::check()) {
        $roleId = (int) Auth::user()->role_id;
        $isAdminOrWaka = in_array($roleId, [1, 2]);

        $layout = match ($roleId) {
            1 => 'layouts.admin',
            2 => 'layouts.waka',
            3, 4 => 'layouts.guru',
            5 => 'layouts.pic',
            default => 'layouts.public',
        };
    }
@endphp

@extends($layout)

@section('title', 'Verifikasi Peminjaman | RAPI Sekolah')
@section('page_title', 'Persetujuan Peminjaman')
@section('page_subtitle', 'Tinjau pengajuan peminjaman ruangan')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                <h4 class="fw-bold mb-3">Verifikasi Peminjaman Ruangan</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Detail Pengajuan --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Agenda</label>
                    <input type="text" class="form-control"
                           value="{{ $event->event_name }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Pemohon</label>
                    <input type="text" class="form-control"
                           value="{{ $event->reporter->fullname ?? '-' }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ruangan yang Diajukan</label>
                    <input type="text" class="form-control"
                           value="{{ $event->room->room_name ?? '-' }}" readonly>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="text" class="form-control"
                               value="{{ $event->date?->format('d M Y') ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Waktu</label>
                        <input type="text" class="form-control"
                               value="{{ $event->start_time }} - {{ $event->end_time }}" readonly>
                    </div>
                </div>

                {{-- Foto Surat --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto Surat Peminjaman</label>

                    @if($event->surat_path)
                        <div>
                            <a href="{{ asset('storage/' . $event->surat_path) }}"
                               target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-file-image"></i> Lihat Surat Peminjaman
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            Surat peminjaman belum diunggah.
                        </div>
                    @endif
                </div>

                @if($isAdminOrWaka)
                    <hr class="my-4">

                    <form action="{{ route('events.update', $event->id) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Pemindahan Ruangan --}}
                        <div class="mb-3">
                            <label for="room_id" class="form-label fw-semibold">
                                Ruangan
                            </label>

                            <select name="room_id" id="room_id" class="form-select" required>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ old('room_id', $event->room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="move_reason" class="form-label fw-semibold">
                                Alasan Pemindahan Ruangan
                            </label>

                            <textarea name="move_reason"
                                      id="move_reason"
                                      rows="2"
                                      class="form-control"
                                      placeholder="Isi jika ruangan dipindahkan">{{ old('move_reason', $event->move_reason) }}</textarea>
                        </div>

                        {{-- Keputusan --}}
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold text-primary">
                                Keputusan Persetujuan
                            </label>

                            <select name="status" id="status"
                                    class="form-select" required
                                    onchange="toggleReasonField()">
                                <option value="Pending" {{ old('status', $event->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Disetujui" {{ old('status', $event->status) == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="Ditolak" {{ old('status', $event->status) == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="mb-4 {{ old('status', $event->status) == 'Ditolak' ? '' : 'd-none' }}"
                             id="reason-container">
                            <label for="rejection_reason"
                                   class="form-label fw-semibold text-danger">
                                Alasan Penolakan
                            </label>

                            <textarea name="rejection_reason"
                                      id="rejection_reason"
                                      rows="3"
                                      class="form-control"
                                      placeholder="Tuliskan alasan penolakan">{{ old('rejection_reason', $event->rejection_reason) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('events.index') }}"
                               class="btn btn-outline-secondary">
                                Kembali
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Keputusan
                            </button>
                        </div>
                    </form>
                @else
                    <a href="{{ route('events.index') }}"
                       class="btn btn-outline-secondary">
                        Kembali
                    </a>
                @endif

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleReasonField() {
    const status = document.getElementById('status');
    const container = document.getElementById('reason-container');
    const reason = document.getElementById('rejection_reason');

    if (!status || !container || !reason) return;

    if (status.value === 'Ditolak') {
        container.classList.remove('d-none');
        reason.required = true;
    } else {
        container.classList.add('d-none');
        reason.required = false;
    }
}

document.addEventListener('DOMContentLoaded', toggleReasonField);
</script>
@endpush

@endsection
