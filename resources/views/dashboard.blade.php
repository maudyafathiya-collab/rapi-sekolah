
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPI Sekolah</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <header class="public-topbar shadow-sm">
        <a href="{{ route('public.dashboard') }}" class="brand-logo">
            <i class="bi bi-building-fill-gear"></i>
            RAPI Sekolah
        </a>
    </header>

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
    
<!-- KALENDER PUBLIC -->
<div id="calendar-section" class="pt-3">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 calendar-card-body">

            <div class="d-flex justify-content-between align-items-center mb-4 calendar-section-heading">
                <h4 class="fw-bold mb-0">Kalender Agenda Sekolah</h4>
                <span class="text-muted small">
                    Jadwal kegiatan dan peminjaman ruangan
                </span>
            </div>

            <!-- Kalender bulanan -->
            <div id="public-calendar"></div>

            <!-- Filter agenda -->
            <div class="calendar-agenda-filter mt-4">
                <h5 class="fw-bold mb-3">Daftar Agenda Sekolah</h5>

                <div class="row g-3 mb-3">

                    <div class="col-12 col-md-4">
                        <label for="publicAgendaFilterType" class="form-label">
                            Tampilkan agenda
                        </label>

                        <select id="publicAgendaFilterType" class="form-select">
                            <option value="month">Per Bulan</option>
                            <option value="day">Per Hari</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4" id="publicAgendaMonthWrapper">
                        <label for="publicAgendaMonth" class="form-label">
                            Pilih Bulan
                        </label>

                        <input type="month" id="publicAgendaMonth" class="form-control">
                    </div>

                    <div class="col-12 col-md-4" id="publicAgendaDateWrapper" hidden>
                        <label for="publicAgendaDate" class="form-label">
                            Pilih Tanggal
                        </label>

                        <input type="date" id="publicAgendaDate" class="form-control">
                    </div>

                </div>

                <div id="publicAgendaResults" class="calendar-agenda-results">
                    <p class="text-muted mb-0">Memuat agenda...</p>
                </div>
            </div>

        </div>
    </div>
</div>


    </main>

    
<!-- FullCalendar JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('public-calendar');

    if (!calendarEl) return;

    const filterType = document.getElementById('publicAgendaFilterType');
    const monthInput = document.getElementById('publicAgendaMonth');
    const dateInput = document.getElementById('publicAgendaDate');
    const monthWrapper = document.getElementById('publicAgendaMonthWrapper');
    const dateWrapper = document.getElementById('publicAgendaDateWrapper');
    const agendaResults = document.getElementById('publicAgendaResults');

    let calendar = null;

    function dateKey(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function formatDate(dateString) {
        const date = new Date(dateString + 'T00:00:00');

        return date.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    // Membuat titik hijau pada tanggal yang memiliki agenda.
    function updateAgendaMarkers() {
        if (!calendar) return;

        calendarEl.querySelectorAll('.calendar-agenda-marker').forEach(function (marker) {
            marker.remove();
        });

        const datesWithEvents = new Set();

        calendar.getEvents().forEach(function (event) {
            if (!event.start) return;

            datesWithEvents.add(dateKey(event.start));
        });

        datesWithEvents.forEach(function (date) {
            const dayCell = calendarEl.querySelector(
                `.fc-daygrid-day[data-date="${date}"]`
            );

            if (!dayCell) return;

            const dayFrame = dayCell.querySelector('.fc-daygrid-day-frame');

            if (!dayFrame) return;

            const marker = document.createElement('span');

            marker.className = 'calendar-agenda-marker';
            marker.title = 'Ada agenda pada tanggal ini';
            marker.setAttribute('aria-label', 'Ada agenda');

            dayFrame.appendChild(marker);
        });
    }

    // Menampilkan agenda sesuai pilihan filter.
    function renderAgenda() {
        if (!calendar) return;

        const selectedType = filterType.value;
        const selectedMonth = monthInput.value;
        const selectedDate = dateInput.value;

        const filteredEvents = calendar.getEvents().filter(function (event) {
            if (!event.start) return false;

            const eventDate = dateKey(event.start);

            if (selectedType === 'day') {
                return eventDate === selectedDate;
            }

            return eventDate.substring(0, 7) === selectedMonth;
        });

        filteredEvents.sort(function (a, b) {
            return a.start - b.start;
        });

        agendaResults.innerHTML = '';

        if (filteredEvents.length === 0) {
            const emptyMessage = document.createElement('p');

            emptyMessage.className = 'text-muted mb-0';
            emptyMessage.textContent = 'Tidak ada agenda pada periode yang dipilih.';

            agendaResults.appendChild(emptyMessage);
            return;
        }

        filteredEvents.forEach(function (event) {
            const item = document.createElement('div');
            item.className = 'calendar-agenda-item';

            const title = document.createElement('strong');
            title.textContent = event.title;

            const date = document.createElement('div');
            date.className = 'small text-muted mt-1';
            date.textContent = formatDate(dateKey(event.start));

            const time = document.createElement('div');
            time.className = 'small mt-1';

            const startTime = event.start.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const endTime = event.end
                ? event.end.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                })
                : '';

            time.textContent = endTime
                ? `${startTime} - ${endTime}`
                : startTime;

            const room = document.createElement('div');
            room.className = 'small mt-1';
            room.textContent = 'Ruangan: ' +
                (event.extendedProps.room ?? '-');

            const status = document.createElement('div');
            status.className = 'small text-muted mt-1';
            status.textContent = 'Status: ' +
                (event.extendedProps.status ?? '-');

            item.append(title, date, time, room, status);
            agendaResults.appendChild(item);
        });
    }

    function updateFilterDisplay() {
        const isDay = filterType.value === 'day';

        monthWrapper.hidden = isDay;
        dateWrapper.hidden = !isDay;

        renderAgenda();
    }

    // Membuat kalender public dengan konfigurasi sama seperti kalender role.
    function createCalendar(holidays) {

        const holidayMap = {};

        holidays.forEach(function (holiday) {
            holidayMap[holiday.date] = holiday;
        });

        calendar = new FullCalendar.Calendar(calendarEl, {

            initialView: 'dayGridMonth',
            locale: 'id',
            firstDay: 0,
            height: 'auto',
            contentHeight: 'auto',
            expandRows: true,
            fixedWeekCount: false,

            headerToolbar: {
                left: 'title',
                center: '',
                right: 'today prev,next'
            },

            buttonText: {
                today: 'Hari ini'
            },

            events: "{{ route('calendar.events') }}",

            // Agenda ditampilkan sebagai titik, bukan teks di kotak tanggal.
            eventContent: function () {
                return {
                    html: ''
                };
            },

            eventsSet: function () {
                updateAgendaMarkers();
                renderAgenda();
            },

            datesSet: function (info) {
                const currentMonth = dateKey(info.view.currentStart).substring(0, 7);

                monthInput.value = currentMonth;

                updateAgendaMarkers();

                if (filterType.value === 'month') {
                    renderAgenda();
                }
            },

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

            dateClick: function (info) {
                filterType.value = 'day';
                dateInput.value = info.dateStr;

                updateFilterDisplay();

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

            eventClick: function (info) {
                alert(
                    'Agenda: ' + info.event.title +
                    '\nRuangan: ' + (info.event.extendedProps.room ?? '-') +
                    '\nStatus: ' + (info.event.extendedProps.status ?? '-')
                );
            }
        });

        calendar.render();

        const today = dateKey(new Date());
        dateInput.value = today;

        window.addEventListener('resize', function () {
            calendar.updateSize();
        });
    }

    // Filter Per Hari atau Per Bulan.
    filterType.addEventListener('change', function () {
        updateFilterDisplay();
    });

    // Saat bulan dipilih, kalender ikut berpindah bulan.
    monthInput.addEventListener('change', function () {
        if (!monthInput.value || !calendar) return;

        calendar.gotoDate(monthInput.value + '-01');
        renderAgenda();
    });

    // Saat tanggal dipilih, kalender ikut berpindah tanggal.
    dateInput.addEventListener('change', function () {
        if (dateInput.value && calendar) {
            calendar.gotoDate(dateInput.value);
        }

        renderAgenda();
    });

    // Mengambil data hari libur.
    fetch("{{ route('calendar.holidays') }}")
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Gagal mengambil data libur.');
            }

            return response.json();
        })
        .then(function (holidays) {
            createCalendar(holidays);
        })
        .catch(function (error) {
            console.error(error);

            alert('Data hari libur gagal dimuat. Kalender agenda tetap ditampilkan.');

            createCalendar([]);
        });

});
</script>


</body>
</html>
