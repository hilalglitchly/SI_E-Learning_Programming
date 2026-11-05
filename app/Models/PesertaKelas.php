<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaKelas extends Model
{
    protected $table = 'tb_peserta_kelas';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'nim',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'nim', 'nim');
    }
}

