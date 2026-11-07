@php
    $isLoggedIn = Auth::check();
    $layout = 'layouts.public';

    if ($isLoggedIn) {
    $layout = match ((int) Auth::user()->role_id) {
        1, 2 => 'layouts.admin',
        3, 4 => 'layouts.guru',
        5 => 'layouts.pic',
        default => 'layouts.public',
    };
}
@endphp

@extends($layout)

@section('title', 'Kalender Agenda - RAPI Sekolah')
@section('page_title', 'Kalender Agenda')
@section('page_subtitle', 'Jadwal kegiatan dan peminjaman ruangan')

@section('content')

<div class="container-fluid px-0 calendar-content-wrapper">
    
    {{-- Banner Selamat Datang HANYA MUNCUL jika BELUM LOGIN (Sebagai Landing Page Publik) --}}
    @if(!$isLoggedIn)
        <div class="card mb-4 border-0 bg-primary text-white shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <h2 class="fw-bold mb-2">Selamat Datang di Portal RAPI Sekolah</h2>
                <p class="mb-4 opacity-75">
                    Sistem Informasi Peminjaman Ruangan & Manajemen Sarana Prasarana Terintegrasi.
                </p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4">
                        <div class="text-primary mb-3 fs-3">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Kalender Agenda Ruangan</h5>
                        <p class="text-muted small mb-4">
                            Pantau jadwal pemakaian ruangan dan ketersediaan fasilitas sekolah secara langsung.
                        </p>
                        <a href="#calendar-section" class="text-decoration-none fw-semibold text-primary">
                            Buka Kalender Agenda &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4">
                        <div class="text-primary mb-3 fs-3">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Akses Layanan Pengguna</h5>
                        <p class="text-muted small mb-4">
                            Masuk dengan akun terdaftar untuk melakukan pengajuan atau verifikasi data.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-4">
                            Masuk ke Akun &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Bagian Kalender (Tampil untuk Publik maupun yang sudah login di Dashboard) --}}
    <div id="calendar-section" class="pt-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Kalender Agenda Sekolah</h4>
                    <span class="text-muted small">Jadwal kegiatan dan peminjaman ruangan</span>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
   
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        if (!calendarEl) {
            return;
        }

        // Mengambil data libur nasional dan cuti bersama
        fetch("{{ route('calendar.holidays') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data libur.');
                }
                return response.json();
            })
            .then(holidays => {
                const holidayMap = {};

                holidays.forEach(holiday => {
                    holidayMap[holiday.date] = holiday;
                });

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    height: 'auto',

                    // Mengambil agenda yang sudah disetujui
                    events: "{{ route('calendar.events') }}",

                    // Memberi warna merah pada akhir pekan dan hari libur
                    dayCellDidMount: function (info) {
                        const date = info.el.getAttribute('data-date');
                        const day = info.date.getDay();
                        const holiday = holidayMap[date];

                        if (day === 0 || day === 6 || holiday) {
                            info.el.style.backgroundColor = '#fee2e2';
                        }

                        const dayNumber = info.el.querySelector('.fc-daygrid-day-number');

                        if (dayNumber) {
                            dayNumber.style.color =
                                (day === 0 || day === 6 || holiday)
                                    ? '#b91c1c'
                                    : '#1f2937';

                            dayNumber.style.fontWeight = '700';
                        }
                    },

                    // Menampilkan informasi saat tanggal diklik
                    dateClick: function (info) {
                        const holiday = holidayMap[info.dateStr];

                        if (holiday) {
                            const jenis = holiday.type === 'collective'
                                ? 'Cuti Bersama'
                                : 'Libur Nasional';

                            alert(
                                'Tanggal: ' + info.dateStr +
                                '\nNama: ' + holiday.name +
                                '\nJenis: ' + jenis
                            );
                        }
                    },

                    // Menampilkan informasi saat agenda diklik
                    eventClick: function (info) {
                        alert(
                            'Agenda: ' + info.event.title +
                            '\nRuangan: ' + (info.event.extendedProps.room ?? '-') +
                            '\nStatus: ' + (info.event.extendedProps.status ?? '-')
                        );
                    }
                });

                calendar.render();
            })
            .catch(error => {
                console.error(error);

                alert('Data hari libur gagal dimuat. Silakan muat ulang halaman.');

                // Kalender agenda tetap ditampilkan meskipun data libur gagal dimuat
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    firstDay: 1,
                    height: 'auto',
                    events: "{{ route('calendar.events') }}"
                });

                calendar.render();
            });
    });
</script>

@endpush