
@php
    $layout = match ((int) Auth::user()->role_id) {
        1 => 'layouts.admin',
        2 => 'layouts.waka',
        3, 4 => 'layouts.guru',
        default => 'layouts.public',
    };
@endphp

@extends($layout)

@section('title', 'Ajukan Peminjaman | RAPI Sekolah')
@section('page_title', 'Ajukan Peminjaman')
@section('page_subtitle', 'Formulir pengajuan peminjaman ruangan')

@section('content')

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <form action="{{ route('events.store') }}" method="POST"
              enctype="multipart/form-data">
            @csrf

            {{-- NAMA KEGIATAN --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Kegiatan</label>

                <input type="text" name="event_name"
                    class="form-control @error('event_name') is-invalid @enderror"
                    value="{{ old('event_name') }}"
                    placeholder="Masukkan nama kegiatan" required>

                @error('event_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- PILIH RUANGAN --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Ruangan</label>

                <select name="room_id"
                    class="form-select @error('room_id') is-invalid @enderror"
                    required>

                    <option value="">-- Pilih Ruangan --</option>

                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->room_name }}
                            (Kapasitas: {{ $room->capacity }})
                        </option>
                    @endforeach
                </select>

                @error('room_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- TANGGAL KEGIATAN --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal Kegiatan</label>

                <input type="date" name="date"
                    class="form-control @error('date') is-invalid @enderror"
                    value="{{ old('date') }}"
                    min="{{ date('Y-m-d') }}" required>

                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- JAM KEGIATAN --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Jam Mulai</label>

                    <input type="time" name="start_time"
                        class="form-control @error('start_time') is-invalid @enderror"
                        value="{{ old('start_time') }}" required>

                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Jam Selesai</label>

                    <input type="time" name="end_time"
                        class="form-control @error('end_time') is-invalid @enderror"
                        value="{{ old('end_time') }}" required>

                    @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- SURAT PEMINJAMAN --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-camera"></i> Surat Peminjaman
                </label>

                <div class="alert alert-info small">
                    Ambil foto surat menggunakan kamera atau unggah dokumen PDF.
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="button" id="startCamera"
                        class="btn btn-outline-primary">
                        <i class="bi bi-camera-video"></i> Aktifkan Kamera
                    </button>

                    <button type="button" id="takePhoto"
                        class="btn btn-primary" disabled>
                        <i class="bi bi-camera-fill"></i> Ambil Foto
                    </button>
                </div>

                <div id="cameraContainer" class="d-none">
                    <video id="cameraPreview"
                        class="w-100 rounded border"
                        autoplay playsinline muted></video>
                </div>

                <canvas id="photoCanvas" class="d-none"></canvas>

                <div id="photoPreviewContainer" class="d-none mt-3">
                    <p class="fw-semibold small">Hasil foto:</p>

                    <img id="photoPreview"
                        class="img-fluid rounded border"
                        style="max-height:300px"
                        alt="Foto surat peminjaman">

                    <button type="button" id="retakePhoto"
                        class="btn btn-outline-secondary btn-sm mt-2">
                        Ambil Ulang
                    </button>
                </div>

                <input type="file" name="surat" id="surat"
                    accept="image/jpeg,image/png,.pdf"
                    class="form-control mt-3 @error('surat') is-invalid @enderror"
                    required>

                <small class="text-muted">
                    Pilih PDF dari perangkat atau gunakan kamera untuk foto surat.
                    Maksimal 2 MB.
                </small>

                @error('surat')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror

                <div id="cameraMessage" class="form-text mt-2">
                    Belum ada surat yang dipilih.
                </div>
            </div>

            {{-- INFORMASI STATUS --}}
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i>
                Pengajuan akan berstatus <strong>Pending</strong>
                sampai diverifikasi Admin atau Waka.
                <br>
                <small>
                    Sistem akan memeriksa ketersediaan ruangan sebelum
                    pengajuan dikirim.
                </small>
            </div>

            {{-- TOMBOL --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('events.index') }}"
                    class="btn btn-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i> Kirim Pengajuan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const startButton = document.getElementById('startCamera');
    const takeButton = document.getElementById('takePhoto');
    const retakeButton = document.getElementById('retakePhoto');

    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('photoCanvas');
    const input = document.getElementById('surat');

    const cameraContainer = document.getElementById('cameraContainer');
    const previewContainer = document.getElementById('photoPreviewContainer');
    const preview = document.getElementById('photoPreview');
    const message = document.getElementById('cameraMessage');

    let stream = null;
    let previewUrl = null;

    // Menghentikan kamera.
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        video.srcObject = null;
        takeButton.disabled = true;
    }

    // Membersihkan preview foto sebelumnya.
    function clearPreview() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        preview.removeAttribute('src');
        previewContainer.classList.add('d-none');
    }

    // Mengaktifkan kamera.
    startButton.addEventListener('click', async function () {

        if (!navigator.mediaDevices?.getUserMedia) {
            message.textContent =
                'Kamera membutuhkan HTTPS atau localhost.';
            return;
        }

        try {
            stopCamera();

            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' }
                },
                audio: false
            });

            video.srcObject = stream;

            cameraContainer.classList.remove('d-none');
            clearPreview();

            takeButton.disabled = false;

            message.textContent =
                'Kamera aktif. Arahkan ke surat peminjaman.';

        } catch (error) {
            message.textContent =
                'Kamera tidak bisa diakses. Periksa izin kamera browser.';
        }
    });

    // Mengambil foto surat.
    takeButton.addEventListener('click', function () {

        if (!stream || !video.videoWidth) {
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');

        context.drawImage(
            video,
            0,
            0,
            canvas.width,
            canvas.height
        );

        canvas.toBlob(function (blob) {

            if (!blob) {
                message.textContent = 'Gagal mengambil foto.';
                return;
            }

            const file = new File(
                [blob],
                'surat-peminjaman-' + Date.now() + '.jpg',
                { type: 'image/jpeg' }
            );

            const transfer = new DataTransfer();
            transfer.items.add(file);

            input.files = transfer.files;

            clearPreview();

            previewUrl = URL.createObjectURL(blob);
            preview.src = previewUrl;

            previewContainer.classList.remove('d-none');
            cameraContainer.classList.add('d-none');

            message.textContent =
                'Foto surat berhasil diambil.';

            stopCamera();

        }, 'image/jpeg', 0.85);
    });

    // Mengambil ulang foto.
    retakeButton.addEventListener('click', function () {

        input.value = '';
        clearPreview();

        message.textContent =
            'Silakan ambil ulang foto surat.';

        startButton.click();
    });

    // Jika user memilih file dari perangkat.
    input.addEventListener('change', function () {

        stopCamera();
        clearPreview();

        message.textContent = input.files.length
            ? 'File surat berhasil dipilih.'
            : 'Belum ada surat yang dipilih.';
    });

    // Validasi sebelum formulir dikirim.
    input.closest('form').addEventListener('submit', function (event) {

        if (!input.files.length) {
            event.preventDefault();

            message.textContent =
                'Surat peminjaman wajib diunggah.';

            message.classList.add('text-danger');
            return;
        }

        stopCamera();
    });

    // Membersihkan kamera saat halaman ditinggalkan.
    window.addEventListener('beforeunload', function () {
        stopCamera();
        clearPreview();
    });

});
</script>
@endpush
