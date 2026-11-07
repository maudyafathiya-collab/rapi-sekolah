
@php
    $roleId = (int) Auth::user()->role_id;
    $isPIC = $roleId === 5;

    $layout = match ($roleId) {
        1 => 'layouts.admin',
        2 => 'layouts.waka',
        3, 4 => 'layouts.guru',
        5 => 'layouts.pic',
        default => 'layouts.public',
    };
@endphp

@extends($layout)

@section('title', 'Detail Pengaduan | RAPI Sekolah')
@section('page_title', 'Detail Pengaduan')
@section('page_subtitle', 'Informasi laporan dan status perbaikan')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                <h4 class="fw-bold mb-3">Detail Pengaduan</h4>
                <p class="text-muted small mb-4">
                    Data laporan tidak dapat diubah. PIC hanya dapat memperbarui status perbaikan.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Informasi ruangan --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ruangan</label>
                    <input type="text" class="form-control"
                        value="{{ $complaint->room->room_name ?? '-' }}"
                        readonly>
                </div>

                {{-- Informasi pelapor --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pelapor</label>
                    <input type="text" class="form-control"
                        value="{{ $complaint->reporter->fullname ?? '-' }}"
                        readonly>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi Pengaduan</label>
                    <textarea class="form-control" rows="4" readonly>{{ $complaint->description }}</textarea>
                </div>

                {{-- PIC read-only --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-person-badge"></i>
                        PIC Penanganan
                    </label>

                    <input type="text" class="form-control"
                        value="{{ $complaint->handler->fullname ?? 'Belum Ditugaskan' }}"
                        readonly>

                    <div class="form-text">
                        Penugasan PIC hanya dapat dilakukan oleh Admin atau Waka.
                    </div>
                </div>

                {{-- Foto laporan --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto Kerusakan</label>

                    @if($complaint->photo_path)
                        <div class="mt-2">
                            <img
                                src="{{ asset('storage/' . $complaint->photo_path) }}"
                                alt="Foto kerusakan"
                                class="img-fluid rounded border"
                                style="max-height: 300px;"
                            >
                        </div>
                    @else
                        <p class="text-muted small">
                            Tidak ada foto lampiran.
                        </p>
                    @endif
                </div>

                <hr class="my-4">

                @if($isPIC)

                    {{-- Hanya PIC yang ditugaskan yang boleh mengubah status --}}
                    <form action="{{ route('complaints.update', $complaint->id) }}"
                        method="POST">

                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="status_repair" class="form-label fw-semibold">
                                Status Perbaikan
                            </label>

                            <select name="status_repair"
                                id="status_repair"
                                class="form-select"
                                required>

                                <option value="Belum Diperbaiki"
                                    {{ old('status_repair', $complaint->status_repair) == 'Belum Diperbaiki' ? 'selected' : '' }}>
                                    Belum Diperbaiki
                                </option>

                                <option value="Menunggu Spare Part"
                                    {{ old('status_repair', $complaint->status_repair) == 'Menunggu Spare Part' ? 'selected' : '' }}>
                                    Menunggu Spare Part
                                </option>

                                <option value="Sudah Diperbaiki"
                                    {{ old('status_repair', $complaint->status_repair) == 'Sudah Diperbaiki' ? 'selected' : '' }}>
                                    Sudah Diperbaiki
                                </option>

                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('complaints.index') }}"
                                class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Status
                            </button>
                        </div>

                    </form>

                @else

                    {{-- Pengguna selain PIC hanya melihat informasi --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status Perbaikan</label>

                        <input type="text" class="form-control"
                            value="{{ $complaint->status_repair }}"
                            readonly>
                    </div>

                    <div class="d-flex justify-content-start">
                        <a href="{{ route('complaints.index') }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                @endif

            </div>
        </div>
    </div>
</div>

@endsection
