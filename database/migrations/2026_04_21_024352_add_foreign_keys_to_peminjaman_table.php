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
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ruangan_id')->references('id')->on('ruangan')->cascadeOnDelete();
            $table->foreign('status_id')->references('id')->on('status_peminjaman')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign('peminjaman_user_id_foreign');
            $table->dropForeign('peminjaman_ruangan_id_foreign');
            $table->dropForeign('peminjaman_status_id_foreign');
        });
    }
};
