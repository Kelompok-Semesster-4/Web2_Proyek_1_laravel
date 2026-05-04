<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStatus extends Model
{
    protected $table = 'log_status';
    public $timestamps = false;

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
}
