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
        Schema::table('ruangan_fasilitas', function (Blueprint $table) {
            $table->foreign('ruangan_id')->references('id')->on('ruangan')->cascadeOnDelete();
            $table->foreign('fasilitas_id')->references('id')->on('fasilitas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangan_fasilitas', function (Blueprint $table) {
            $table->dropForeign('ruangan_fasilitas_ruangan_id_foreign');
            $table->dropForeign('ruangan_fasilitas_fasilitas_id_foreign');
        });
    }
};
