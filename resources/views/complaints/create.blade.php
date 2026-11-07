
@php
    $roleId = (int) Auth::user()->role_id;

    $layout = match ($roleId) {
        1 => 'layouts.admin',
        2 => 'layouts.waka',
        3, 4 => 'layouts.guru',
        5 => 'layouts.pic',
        default => 'layouts.public',
    };
@endphp

@extends($layout)

@section('title', 'Tambah Pengaduan | RAPI Sekolah')
@section('page_title', 'Tambah Pengaduan')
@section('page_subtitle', 'Laporkan kerusakan fasilitas sekolah')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                <h4 class="fw-bold mb-2">Form Pengaduan Kerusakan</h4>
                <p class="text-muted small mb-4">
                    Isi laporan kerusakan dan ambil foto langsung menggunakan kamera.
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

                <form id="complaintForm"
                    action="{{ route('complaints.store') }}"
                    method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    {{-- Ruangan --}}
                    <div class="mb-3">
                        <label for="room_id" class="form-label fw-semibold">
                            Ruangan
                        </label>

                        <select name="room_id" id="room_id"
                            class="form-select" required>

                            <option value="">-- Pilih Ruangan --</option>

                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    
{{-- Pelapor --}}
<div class="mb-3">
    <label class="form-label fw-semibold">
        Pelapor
    </label>

    <input type="text"
        class="form-control"
        value="{{ Auth::user()->fullname }}"
        readonly>

    <div class="form-text">
        Nama pelapor diambil otomatis dari akun yang sedang login.
    </div>
</div>


                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">
                            Deskripsi Pengaduan
                        </label>

                        <textarea name="description" id="description"
                            rows="4" class="form-control"
                            placeholder="Jelaskan kerusakan yang terjadi..."
                            required>{{ old('description') }}</textarea>
                    </div>

                    <hr class="my-4">

                    {{-- Kamera --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-camera"></i>
                            Foto Kerusakan
                        </label>

                        <div class="alert alert-info small">
                            Foto harus diambil langsung menggunakan kamera perangkat.
                            Pengiriman laporan tanpa foto tidak diperbolehkan.
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="button" id="startCamera"
                                class="btn btn-outline-primary">
                                <i class="bi bi-camera-video"></i>
                                Aktifkan Kamera
                            </button>

                            <button type="button" id="takePhoto"
                                class="btn btn-primary" disabled>
                                <i class="bi bi-camera-fill"></i>
                                Ambil Foto
                            </button>
                        </div>

                        <div id="cameraContainer" class="d-none">
                            <video id="cameraPreview"
                                class="w-100 rounded border"
                                autoplay playsinline muted></video>
                        </div>

                        <canvas id="photoCanvas" class="d-none"></canvas>

                        <div id="photoPreviewContainer"
                            class="d-none mt-3">

                            <p class="fw-semibold small mb-2">
                                Hasil Foto:
                            </p>

                            <img id="photoPreview"
                                class="img-fluid rounded border"
                                alt="Hasil foto kerusakan"
                                style="max-height: 300px;">

                            <button type="button" id="retakePhoto"
                                class="btn btn-outline-secondary btn-sm mt-2">
                                <i class="bi bi-arrow-repeat"></i>
                                Ambil Ulang
                            </button>
                        </div>

                        {{-- File diisi otomatis dari hasil kamera --}}
                        <input type="file"
                            name="photo_path"
                            id="photo_path"
                            accept="image/jpeg,image/png"
                            class="d-none">

                        <div id="cameraMessage"
                            class="form-text mt-2">
                            Belum ada foto yang diambil.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('complaints.index') }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>

                        <button type="submit" id="submitButton"
                            class="btn btn-primary">
                            <i class="bi bi-send"></i>
                            Kirim Pengaduan
                        </button>
                    </div>

                </form>

            </div>
        </div>
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
    const photoInput = document.getElementById('photo_path');

    const cameraContainer = document.getElementById('cameraContainer');
    const previewContainer = document.getElementById('photoPreviewContainer');
    const photoPreview = document.getElementById('photoPreview');
    const cameraMessage = document.getElementById('cameraMessage');
    const form = document.getElementById('complaintForm');

    let cameraStream = null;

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        video.srcObject = null;
    }

    startButton.addEventListener('click', async function () {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraMessage.textContent =
                'Browser tidak mendukung kamera atau halaman belum menggunakan HTTPS/localhost.';
            return;
        }

        try {
            stopCamera();

            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' }
                },
                audio: false
            });

            video.srcObject = cameraStream;

            cameraContainer.classList.remove('d-none');
            previewContainer.classList.add('d-none');

            takeButton.disabled = false;

            cameraMessage.textContent =
                'Kamera aktif. Silakan arahkan kamera ke bagian yang rusak.';

        } catch (error) {
            cameraMessage.textContent =
                'Kamera tidak dapat diakses. Izinkan akses kamera pada browser dan coba lagi.';
        }
    });

    takeButton.addEventListener('click', function () {
        if (!cameraStream || !video.videoWidth) {
            cameraMessage.textContent = 'Kamera belum siap.';
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(function (blob) {
            if (!blob) {
                cameraMessage.textContent = 'Gagal mengambil foto. Silakan coba lagi.';
                return;
            }

            const file = new File(
                [blob],
                'foto-kerusakan-' + Date.now() + '.jpg',
                { type: 'image/jpeg' }
            );

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            photoInput.files = dataTransfer.files;

            photoPreview.src = URL.createObjectURL(blob);

            previewContainer.classList.remove('d-none');
            cameraContainer.classList.add('d-none');

            takeButton.disabled = true;

            cameraMessage.textContent =
                'Foto berhasil diambil dan siap dikirim.';

            stopCamera();

        }, 'image/jpeg', 0.85);
    });

    retakeButton.addEventListener('click', function () {
        photoInput.value = '';
        previewContainer.classList.add('d-none');
        cameraMessage.textContent = 'Silakan aktifkan kamera untuk mengambil ulang foto.';
        startButton.click();
    });

    form.addEventListener('submit', function (event) {
        if (!photoInput.files || photoInput.files.length === 0) {
            event.preventDefault();

            cameraMessage.textContent =
                'Foto kerusakan wajib diambil menggunakan kamera sebelum mengirim laporan.';

            cameraMessage.classList.add('text-danger');
            return;
        }

        stopCamera();
    });

    window.addEventListener('beforeunload', stopCamera);
});
</script>
@endpush
