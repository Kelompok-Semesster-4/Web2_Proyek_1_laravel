<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'ruangan_id',
        'nama_kegiatan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jumlah_peserta',
        'surat',
        'status_id',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusPeminjaman::class, 'status_id');
    }

    public function logStatus()
    {
        return $this->hasMany(LogStatus::class, 'peminjaman_id');
    }
}
