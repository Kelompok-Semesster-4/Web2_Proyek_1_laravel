<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'status_peminjaman';
    
    public $timestamps = false;

    protected $fillable = [
        'nama_status',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'status_id');
    }

    public function logStatus()
    {
        return $this->hasMany(LogStatus::class, 'status_id');
    }
}
