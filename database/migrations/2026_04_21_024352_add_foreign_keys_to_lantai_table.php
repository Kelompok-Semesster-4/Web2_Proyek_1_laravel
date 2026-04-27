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
        Schema::table('lantai', function (Blueprint $table) {
            $table->foreign('gedung_id')->references('id')->on('gedung')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lantai', function (Blueprint $table) {
            $table->dropForeign('lantai_gedung_id_foreign');
        });
    }
};
