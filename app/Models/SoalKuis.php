<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalKuis extends Model
{
    protected $table = 'tb_soal_kuis';
    protected $primaryKey = 'id_soal';
    public $timestamps = false;

    protected $fillable = [
        'id_kuis',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'jawaban_benar',
    ];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id_kuis');
    }
}

