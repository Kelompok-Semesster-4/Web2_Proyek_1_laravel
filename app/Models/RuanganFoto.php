<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuanganFoto extends Model
{
    protected $table = 'ruangan_foto';
    public $timestamps = false;
    protected $fillable = ['ruangan_id', 'nama_file', 'tipe'];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
