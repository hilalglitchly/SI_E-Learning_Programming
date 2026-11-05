<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiKuis extends Model
{
    protected $table = 'tb_nilai_kuis';
    protected $primaryKey = 'id_nilai';
    public $timestamps = false;

    protected $fillable = [
        'id_kuis',
        'nim',
        'nilai',
        'tgl_dikerjakan',
    ];

    protected $casts = [
        'tgl_dikerjakan' => 'datetime',
    ];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id_kuis');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'nim', 'nim');
    }
}

