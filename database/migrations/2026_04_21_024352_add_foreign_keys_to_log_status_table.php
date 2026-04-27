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
        Schema::table('log_status', function (Blueprint $table) {
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->cascadeOnDelete();
            $table->foreign('status_id')->references('id')->on('status_peminjaman')->restrictOnDelete();
            $table->foreign('diubah_oleh')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_status', function (Blueprint $table) {
            $table->dropForeign('log_status_peminjaman_id_foreign');
            $table->dropForeign('log_status_status_id_foreign');
            $table->dropForeign('log_status_diubah_oleh_foreign');
        });
    }
};
