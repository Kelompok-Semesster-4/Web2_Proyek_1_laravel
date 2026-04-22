<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fasilitas')->insert([
            ['id' => 1, 'nama_fasilitas' => 'Proyektor'],
            ['id' => 2, 'nama_fasilitas' => 'AC'],
            ['id' => 3, 'nama_fasilitas' => 'WiFi'],
            ['id' => 4, 'nama_fasilitas' => 'Sound System'],
            ['id' => 5, 'nama_fasilitas' => 'Papan Tulis'],
            ['id' => 6, 'nama_fasilitas' => 'Mikrofon'],
            ['id' => 7, 'nama_fasilitas' => 'Kursi'],
            ['id' => 8, 'nama_fasilitas' => 'Meja'],
        ]);
    }
}
