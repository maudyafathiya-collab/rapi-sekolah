
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;


// Halaman general yang bisa diakses tanpa login
Route::get('/', function () {
    return view('dashboard');
})->name('public.dashboard');

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginProcess']);

// Kalender yang bisa diakses tanpa login
Route::get('/calendar', [EventController::class, 'calendar'])
    ->name('calendar');

Route::get('/calendar/events', [EventController::class, 'calendarEvents'])
    ->name('calendar.events');

// Data libur nasional dan cuti bersama
Route::get('/calendar/holidays', [EventController::class, 'calendarHolidays'])
    ->name('calendar.holidays');


// Halaman yang wajib login
Route::middleware('auth')->group(function () {

    // Dashboard berdasarkan role pengguna
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Kelola pengguna
    Route::resource('/users', UserController::class)->except(['show']);

    // Kelola ruangan
    Route::resource('/rooms', RoomController::class)->except(['show']);

    // Kelola agenda
    Route::resource('/events', EventController::class)->except(['show']);

    // Pengaduan kerusakan
    Route::get('/complaints/history', [ComplaintController::class, 'history'])
        ->name('complaints.history');

    // Resource pengaduan
    Route::resource('/complaints', ComplaintController::class)->except(['show']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});