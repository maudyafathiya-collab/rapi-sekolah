<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')
                ->constrained('rooms')
                ->onDelete('cascade');

            // ID Guru atau Pengguna yang mengajukan peminjaman
            // (Sebaiknya dinamakan user_id agar lebih umum, tapi jika pakai report_id silakan sesuaikan)
            $table->foreignId('user_id') 
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event_name');
            $table->date('date');

            // --- DIUBAH MENJADI SISTEM SESI (10 Sesi, Jam 07:00 - 17:00) ---
            // Contoh isi: "Sesi 1 (07:00 - 08:00)" atau "Sesi 1 - 3"
            $table->string('session'); 

            $table->text('description')->nullable();

            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])
                ->default('Pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('rejection_reason')->nullable();
            $table->text('move_reason')->nullable(); // Untuk alasan pindah ruangan

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};