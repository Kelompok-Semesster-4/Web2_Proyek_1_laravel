<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogStatus extends Model
{
    use HasFactory;

    protected $table = 'log_status';
    
    public const CREATED_AT = 'waktu';
    public const UPDATED_AT = null;

    protected $fillable = [
        'peminjaman_id',
        'status_id',
        'diubah_oleh',
        'catatan',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusPeminjaman::class, 'status_id');
    }

    public function diubahOleh()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
