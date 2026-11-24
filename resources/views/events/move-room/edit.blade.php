
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

@section('title', 'Formulir Pemindahan Ruangan | RAPI Sekolah')
@section('page_title', 'Pindahkan Ruangan')
@section('page_subtitle', 'Formulir pemindahan ruangan kegiatan')

@section('content')

<div class="mb-4">
    <a href="{{ route('events.move-room.index') }}"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Periksa kembali data berikut:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-arrow-left-right me-2"></i>
            Formulir Pemindahan Ruangan
        </h5>
    </div>

    <div class="card-body p-4">

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-1"></i>
            Pemindahan hanya dapat dilakukan untuk agenda yang sudah
            disetujui dan belum dimulai.
        </div>

        <h6 class="fw-bold mb-3">Informasi Kegiatan</h6>

        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Nama Kegiatan</label>
                <input type="text"
                       class="form-control"
                       value="{{ $event->event_name }}"
                       readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Nama Pemohon</label>
                <input type="text"
                       class="form-control"
                       value="{{ $event->reporter->fullname ?? '-' }}"
                       readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Tanggal Kegiatan</label>
                <input type="text"
                       class="form-control"
                       value="{{ \Carbon\Carbon::parse($event->date)->format('d F Y') }}"
                       readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Waktu Kegiatan</label>
                <input type="text"
                       class="form-control"
                       value="{{ substr($event->start_time, 0, 5) }} - {{ substr($event->end_time, 0, 5) }}"
                       readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Ruangan Saat Ini</label>
                <input type="text"
                       class="form-control"
                       value="{{ $event->room->room_name ?? '-' }}"
                       readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Status Peminjaman</label>
                <div>
                    <span class="badge bg-success fs-6">
                        {{ $event->status }}
                    </span>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <form action="{{ route('events.update', $event->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <input type="hidden" name="action" value="move_room">

            <h6 class="fw-bold mb-3">Informasi Pemindahan</h6>

            <div class="mb-3">
                <label for="room_id" class="form-label fw-semibold">
                    Pilih Ruangan Baru
                    <span class="text-danger">*</span>
                </label>

                <select name="room_id"
                        id="room_id"
                        class="form-select @error('room_id') is-invalid @enderror"
                        required>

                    <option value="">-- Pilih Ruangan --</option>

                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->room_name }}
                            (Kapasitas: {{ $room->capacity }} orang)
                        </option>
                    @endforeach

                </select>

                @error('room_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if($rooms->isEmpty())
                    <small class="text-danger">
                        Belum ada ruangan aktif lain yang tersedia sebagai pilihan.
                    </small>
                @endif
            </div>

            <div class="mb-4">
                <label for="move_reason" class="form-label fw-semibold">
                    Alasan Pemindahan
                    <span class="text-danger">*</span>
                </label>

                <textarea name="move_reason"
                          id="move_reason"
                          rows="4"
                          class="form-control @error('move_reason') is-invalid @enderror"
                          placeholder="Tuliskan alasan pemindahan ruangan..."
                          required>{{ old('move_reason') }}</textarea>

                @error('move_reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('events.move-room.index') }}"
                   class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-warning"
                        {{ $rooms->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Simpan Pemindahan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
