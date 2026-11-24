
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

    {{-- Banner Selamat Datang hanya muncul jika belum login --}}
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

    {{-- Kalender untuk publik maupun pengguna yang sudah login --}}
    <div id="calendar-section" class="pt-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 calendar-card-body">

                <div class="d-flex justify-content-between align-items-center mb-4 calendar-section-heading">
                    <h4 class="fw-bold mb-0">Kalender Agenda Sekolah</h4>
                    <span class="text-muted small">
                        Jadwal kegiatan dan peminjaman ruangan
                    </span>
                </div>

                {{-- Kalender bulanan --}}
                <div id="calendar"></div>

                {{-- Filter agenda --}}
                <div class="calendar-agenda-filter mt-4">
                    <h5 class="fw-bold mb-3">Daftar Agenda Sekolah</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="agendaFilterType" class="form-label">
                                Tampilkan agenda
                            </label>
                            <select id="agendaFilterType" class="form-select">
                                <option value="month">Per Bulan</option>
                                <option value="day">Per Hari</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4" id="agendaMonthWrapper">
                            <label for="agendaMonth" class="form-label">
                                Pilih Bulan
                            </label>
                            <input type="month" id="agendaMonth" class="form-control">
                        </div>

                        <div class="col-12 col-md-4" id="agendaDateWrapper" hidden>
                            <label for="agendaDate" class="form-label">
                                Pilih Tanggal
                            </label>
                            <input type="date" id="agendaDate" class="form-control">
                        </div>
                    </div>

                    <div id="agendaResults" class="calendar-agenda-results">
                        <p class="text-muted mb-0">Memuat agenda...</p>
                    </div>
                </div>

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

            const filterType = document.getElementById('agendaFilterType');
            const monthInput = document.getElementById('agendaMonth');
            const dateInput = document.getElementById('agendaDate');
            const monthWrapper = document.getElementById('agendaMonthWrapper');
            const dateWrapper = document.getElementById('agendaDateWrapper');
            const agendaResults = document.getElementById('agendaResults');

            let calendar = null;

            // Mengubah tanggal menjadi format YYYY-MM-DD
            function dateKey(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            // Format tanggal Indonesia
            function formatDate(dateString) {
                const date = new Date(dateString + 'T00:00:00');

                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            }

            // Membuat satu titik untuk setiap tanggal yang memiliki agenda
            function updateAgendaMarkers() {
                if (!calendar) {
                    return;
                }

                calendarEl.querySelectorAll('.calendar-agenda-marker').forEach(function (marker) {
                    marker.remove();
                });

                const datesWithEvents = new Set();

                calendar.getEvents().forEach(function (event) {
                    if (!event.start) {
                        return;
                    }

                    datesWithEvents.add(dateKey(event.start));
                });

                datesWithEvents.forEach(function (date) {
                    const dayCell = calendarEl.querySelector(
                        `.fc-daygrid-day[data-date="${date}"]`
                    );

                    if (!dayCell) {
                        return;
                    }

                    const dayFrame = dayCell.querySelector('.fc-daygrid-day-frame');

                    if (!dayFrame) {
                        return;
                    }

                    const marker = document.createElement('span');
                    marker.className = 'calendar-agenda-marker';
                    marker.title = 'Ada agenda pada tanggal ini';
                    marker.setAttribute('aria-label', 'Ada agenda');

                    dayFrame.appendChild(marker);
                });
            }

            // Menampilkan daftar agenda berdasarkan filter
            function renderAgenda() {
                if (!calendar) {
                    return;
                }

                const selectedType = filterType.value;
                const selectedMonth = monthInput.value;
                const selectedDate = dateInput.value;

                const filteredEvents = calendar.getEvents().filter(function (event) {
                    if (!event.start) {
                        return false;
                    }

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

                    if (event.allDay) {
                        time.textContent = 'Sepanjang hari';
                    } else {
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
                    }

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

            // Mengatur filter yang sedang aktif
            function updateFilterDisplay() {
                const isDay = filterType.value === 'day';

                monthWrapper.hidden = isDay;
                dateWrapper.hidden = !isDay;

                renderAgenda();
            }

            // Membuat kalender
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

                    // Mengambil agenda yang sudah disetujui
                    events: "{{ route('calendar.events') }}",

                    // Menyembunyikan teks agenda dari kotak tanggal.
                    // Penanda agenda akan ditampilkan sebagai titik hijau.
                    eventContent: function () {
                        return {
                            html: ''
                        };
                    },

                    // Memperbarui titik dan daftar setelah data agenda dimuat
                    eventsSet: function () {
                        updateAgendaMarkers();
                        renderAgenda();
                    },

                    // Menyesuaikan bulan filter saat kalender berpindah bulan
                    datesSet: function (info) {
                        const currentMonth = dateKey(info.view.currentStart).substring(0, 7);

                        monthInput.value = currentMonth;

                        updateAgendaMarkers();

                        if (filterType.value === 'month') {
                            renderAgenda();
                        }
                    },

                    // Warna akhir pekan dan hari libur tetap dipertahankan
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

                    // Klik tanggal langsung menampilkan agenda pada tanggal tersebut
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
                    }
                });

                calendar.render();

                // Nilai awal filter tanggal adalah hari ini
                const today = dateKey(new Date());
                dateInput.value = today;

                // Perbarui ukuran kalender saat layar berubah
                window.addEventListener('resize', function () {
                    calendar.updateSize();
                });
            }

            // Pilihan filter Per Hari atau Per Bulan
            filterType.addEventListener('change', function () {
                updateFilterDisplay();
            });

            // Saat bulan dipilih, kalender ikut berpindah ke bulan tersebut
            monthInput.addEventListener('change', function () {
                if (!monthInput.value || !calendar) {
                    return;
                }

                calendar.gotoDate(monthInput.value + '-01');
                renderAgenda();
            });

            // Saat tanggal dipilih, kalender ikut berpindah ke tanggal tersebut
            dateInput.addEventListener('change', function () {
                if (dateInput.value && calendar) {
                    calendar.gotoDate(dateInput.value);
                }

                renderAgenda();
            });

            // Mengambil data libur nasional dan cuti bersama
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

                    // Kalender tetap tampil meskipun data libur gagal dimuat
                    createCalendar([]);
                });
        });
    </script>
@endpush
