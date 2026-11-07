<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->dropColumn('pic_id');

            $table->text('rejection_reason')->nullable();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->dropColumn('pic_id');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('pic_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('pic_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dropColumn('rejection_reason');
        });
    }
};