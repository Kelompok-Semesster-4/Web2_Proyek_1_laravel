<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    protected $table = 'gedung';
    public $timestamps = false;
    protected $fillable = ['nama_gedung'];

    public function lantai()
    {
        return $this->hasMany(Lantai::class, 'gedung_id');
    }
}
