<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPeminjaman extends Model
{
    protected $table = 'status_peminjaman';
    public $timestamps = false;
    protected $fillable = ['nama_status'];
}
