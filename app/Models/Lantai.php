<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lantai extends Model
{
    use HasFactory;

    protected $table = 'lantai';
    
    public $timestamps = false;

    protected $fillable = [
        'gedung_id',
        'nomor',
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function ruangan()
    {
        return $this->hasMany(Ruangan::class, 'lantai_id');
    }
}
