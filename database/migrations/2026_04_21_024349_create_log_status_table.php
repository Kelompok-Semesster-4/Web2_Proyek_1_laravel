<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id');
            $table->foreignId('status_id');
            $table->foreignId('diubah_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('waktu')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_status');
    }
};
