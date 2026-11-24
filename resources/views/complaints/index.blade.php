@php
    $roleId = Auth::check() ? (int) Auth::user()->role_id : 0;

    $layout = match ($roleId) {
        1 => 'layouts.admin',
        2 => 'layouts.waka',
        3, 4 => 'layouts.guru',
        5 => 'layouts.pic',
        default => 'layouts.public',
    };
@endphp

@extends($layout)

@section('title', 'Data Pengaduan | RAPI Sekolah')
@section('page_title', 'Pengaduan Kerusakan')
@section('page_subtitle', 'Kelola dan pantau laporan pengaduan fasilitas ruangan')

@section('content')

    {{-- Header halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daftar Pengaduan Masuk</h4>
            <p class="text-muted small mb-0">
                Pantau laporan kerusakan sarana dan prasarana dari pengguna.
            </p>
        </div>

        {{-- Tombol tambah hanya untuk Guru dan Organisasi --}}
        @if(in_array($roleId, [3, 4]))
            <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Pengaduan
            </a>
        @endif
    </div>

    {{-- Notifikasi berhasil --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Notifikasi kesalahan validasi --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabel pengaduan --}}
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
                            <th>Status</th>
                            <th>Penanganan PIC</th>

                            @if(in_array($roleId, [1, 2]))
                                <th class="text-end pe-3">Kontak PIC</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($complaints as $complaint)

                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>

                                <td class="fw-medium">
                                    {{ $complaint->room->room_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $complaint->reporter->fullname ?? '-' }}
                                </td>

                                <td>
                                    {{ Str::limit($complaint->description, 40) }}
                                </td>

                                {{-- Status perbaikan --}}
                                <td>
                                    @if($complaint->status_repair === 'Sudah Diperbaiki')
                                        <span class="badge bg-success">
                                            Sudah Diperbaiki
                                        </span>
                                    @elseif($complaint->status_repair === 'Menunggu Spare Part')
                                        <span class="badge bg-warning text-dark">
                                            Menunggu Spare Part
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Belum Diperbaiki
                                        </span>
                                    @endif
                                </td>

                                {{-- Nama PIC --}}
                                <td>
                                    @if($complaint->handler)
                                        <span class="badge bg-success">
                                            {{ $complaint->handler->fullname }}
                                        </span>
                                    @else
                                        <span class="text-muted small">
                                            Belum ada PIC
                                        </span>
                                    @endif
                                </td>

                               
                                {{-- Kontak WhatsApp khusus Admin dan Waka --}}
                                @if(in_array($roleId, [1, 2]))
                                    <td class="text-end pe-3">
                                        @if($complaint->handler && $complaint->handler->phone_number)

                                            @php
                                                $phone = preg_replace(
                                                    '/\D/',
                                                    '',
                                                    $complaint->handler->phone_number
                                                );

                                                // Ubah 08 menjadi 628
                                                if (str_starts_with($phone, '0')) {
                                                    $phone = '62' . substr($phone, 1);
                                                }

                                                // Ubah 8 menjadi 628
                                                elseif (str_starts_with($phone, '8')) {
                                                    $phone = '62' . $phone;
                                                }
                                            @endphp
                                                                      
                                            @php
                                                // Buat pesan WhatsApp pengaduan
                                                $message = "Halo Bapak/Ibu {$complaint->handler->fullname},\n\n"
                                                    . "Kami ingin menginformasikan adanya pengaduan kerusakan fasilitas sekolah.\n\n"
                                                    . "Detail Pengaduan:\n"
                                                    . "Nama PIC: {$complaint->handler->fullname}\n"
                                                    . "Ruangan: " . ($complaint->room->room_name ?? '-') . "\n"
                                                    . "Nama Pelapor: " . ($complaint->reporter->fullname ?? '-') . "\n"
                                                    . "Tanggal Pengaduan: " . $complaint->created_at->format('d-m-Y H:i') . "\n"
                                                    . "Deskripsi Kerusakan: {$complaint->description}\n\n"
                                                    . "Mohon segera dilakukan pengecekan dan penanganan terhadap pengaduan tersebut.\n\n"
                                                    . "Terima kasih.\n"
                                                    . "RAPI Sekolah";

                                                $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
                                            @endphp

                                            <a
                                                href="{{ $whatsappUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-success btn-sm"
                                            >
                                                <i class="bi bi-whatsapp"></i>
                                                Hubungi PIC
                                            </a>


                                        @else
                                            <span class="text-muted small">
                                                Nomor belum tersedia
                                            </span>
                                        @endif
                                    </td>
                                @endif

                                            

                            </tr>

                        @empty
                            <tr>
                                <td colspan="{{ in_array($roleId, [1, 2]) ? 7 : 6 }}"
                                    class="text-center py-4 text-muted">

                                    <i class="bi bi-clipboard-x fs-3 d-block mb-2"></i>
                                    Belum ada data pengaduan.

                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
```