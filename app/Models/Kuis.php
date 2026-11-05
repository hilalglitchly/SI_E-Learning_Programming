<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kuis extends Model
{
    protected $table = 'tb_kuis';
    protected $primaryKey = 'id_kuis';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'judul_kuis',
        'deskripsi',
        'durasi_menit',
        'tgl_dibuat',
    ];

    protected $casts = [
        'tgl_dibuat' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function soal()
    {
        return $this->hasMany(SoalKuis::class, 'id_kuis', 'id_kuis');
    }

    public function nilai()
    {
        return $this->hasMany(NilaiKuis::class, 'id_kuis', 'id_kuis');
    }
}

