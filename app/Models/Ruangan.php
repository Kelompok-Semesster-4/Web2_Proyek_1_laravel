<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    public $timestamps = false;
    protected $fillable = ['lantai_id', 'nama_ruangan', 'kapasitas', 'deskripsi'];

    public function lantai()
    {
        return $this->belongsTo(Lantai::class, 'lantai_id');
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'ruangan_fasilitas', 'ruangan_id', 'fasilitas_id');
    }

    public function foto()
    {
        return $this->hasMany(RuanganFoto::class, 'ruangan_id');
    }
}
