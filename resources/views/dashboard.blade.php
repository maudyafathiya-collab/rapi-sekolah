
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPI Sekolah</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">

    <!-- CSS External -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <!-- TOPBAR GENERAL -->
    <header class="public-topbar shadow-sm">
        <a href="{{ route('public.dashboard') }}" class="brand-logo">
            <i class="bi bi-building-fill-gear"></i>
            RAPI Sekolah
        </a>

    </header>

    <!-- KONTEN UTAMA -->
    <main class="container public-main py-4">

        <!-- WELCOME BANNER -->
        <div class="card mb-4 border-0 bg-primary text-white shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h4 class="fw-bold mb-2 fs-4">
                    Selamat Datang di Portal RAPI Sekolah
                </h4>

                <p class="mb-0 opacity-75 small">
                    Sistem Informasi Peminjaman Ruangan & Manajemen Sarana
                    Prasarana Terintegrasi. Cek jadwal penggunaan ruangan
                    secara terbuka di sini.
                </p>
            </div>
        </div>

        <!-- FITUR UTAMA -->
        <div class="row g-3 mb-4">

            <!-- KARTU KALENDER -->
            <div class="col-md-6">
                <div class="card h-100 p-2">
                    <div class="card-body d-flex flex-column justify-content-between">

                        <div>
                            <div class="public-stat-icon mb-3">
                                <i class="bi bi-calendar-event"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Kalender Agenda Ruangan
                            </h6>

                            <p class="text-muted small mb-3">
                                Pantau jadwal pemakaian ruangan dan
                                ketersediaan fasilitas sekolah secara
                                langsung.
                            </p>
                        </div>

                        <button type="button"
                            class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                            onclick="document.getElementById('calendar-section').scrollIntoView({ behavior: 'smooth' })">

                            Buka Kalender Agenda
                            <i class="bi bi-arrow-right"></i>
                        </button>

                    </div>
                </div>
            </div>

            <!-- KARTU LOGIN -->
            <div class="col-md-6">
                <div class="card h-100 p-2">
                    <div class="card-body d-flex flex-column justify-content-between">

                        <div>
                            <div class="public-stat-icon mb-3">
                                <i class="bi bi-shield-lock"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Akses Layanan Pengguna
                            </h6>

                            <p class="text-muted small mb-3">
                                Masuk dengan akun terdaftar untuk melakukan pengajuan atau verifikasi data.
                            </p>
                        </div>

                        <a href="{{ route('login') }}"
                           class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">

                            Masuk ke Akun
                            <i class="bi bi-box-arrow-in-right"></i>
                        </a>

                    </div>
                </div>
            </div>

        </div>

        <!-- KALENDER PUBLIK -->
        <div class="card border-0 shadow-sm mt-4" id="calendar-section">
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">
                            Kalender Agenda Sekolah
                        </h5>
                    </div>
                </div>

                <!-- Tempat FullCalendar -->
                <div id="public-calendar"></div>

            </div>
        </div>

    </main>

    <!-- FullCalendar JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    
<!-- Kalender Publik -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarElement = document.getElementById('public-calendar');

        if (!calendarElement) return;

        fetch("{{ route('calendar.holidays') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data libur');
                }
                return response.json();
            })
            .then(holidays => {
                const holidayMap = {};

                holidays.forEach(holiday => {
                    holidayMap[holiday.date] = holiday;
                });

                const calendar = new FullCalendar.Calendar(calendarElement, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    height: 'auto',

                    events: "{{ route('calendar.events') }}",

                    dayCellDidMount: function (info) {
                        const date = info.el.getAttribute('data-date');
                        const day = info.date.getDay();
                        const holiday = holidayMap[date];

                        // Sabtu, Minggu, dan hari libur berwarna merah
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

                    dateClick: function (info) {
                        const holiday = holidayMap[info.dateStr];

                        if (holiday) {
                            alert(
                                'Tanggal: ' + info.dateStr +
                                '\nNama: ' + holiday.name +
                                '\nJenis: ' + (
                                    holiday.type === 'collective'
                                        ? 'Cuti Bersama'
                                        : 'Libur Nasional'
                                )
                            );
                        }
                    },

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
                alert('Data hari libur gagal dimuat.');

                // Tetap tampilkan kalender jika data libur gagal dimuat
                const calendar = new FullCalendar.Calendar(calendarElement, {
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


</body>
</html>
