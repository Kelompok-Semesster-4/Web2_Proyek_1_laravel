<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';
    
    public $timestamps = false;

    protected $fillable = [
        'nama_fasilitas',
    ];

    public function ruangan()
    {
        return $this->belongsToMany(Ruangan::class, 'ruangan_fasilitas', 'fasilitas_id', 'ruangan_id');
    }
}
