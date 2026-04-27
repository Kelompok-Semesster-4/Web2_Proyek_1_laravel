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
        Schema::create('ruangan_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id');
            $table->string('nama_file');
            $table->enum('tipe', ['detail', 'cover'])->default('detail');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan_foto');
    }
};
