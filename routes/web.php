
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PicRoomController;


// Halaman general yang bisa diakses tanpa login
Route::get('/', function () {
    return view('dashboard');
})->name('public.dashboard');


// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginProcess']);


// Lupa Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.update');


// Kalender yang bisa diakses tanpa login
Route::get('/calendar', [EventController::class, 'calendar'])
    ->name('calendar');

Route::get('/calendar/events', [EventController::class, 'calendarEvents'])
    ->name('calendar.events');

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


    // Daftar ruangan khusus PIC
    Route::get('/pic/rooms', [PicRoomController::class, 'index'])
        ->name('pic.rooms.index');


    // Kelola agenda dan peminjaman ruangan

    // Halaman daftar agenda yang dapat dipindahkan
    Route::get('/events/move-room', [EventController::class, 'moveRoomIndex'])
        ->name('events.move-room.index');

    // Halaman formulir pemindahan ruangan
    Route::get('/events/move-room/{id}', [EventController::class, 'moveRoomEdit'])
        ->name('events.move-room.edit');

    // Resource peminjaman ruangan
    Route::resource('/events', EventController::class)->except(['show']);


    // Pengaduan kerusakan
    Route::get('/complaints/history', [ComplaintController::class, 'history'])
        ->name('complaints.history');

    Route::resource('/complaints', ComplaintController::class)->except(['show']);


    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
