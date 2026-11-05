<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'tb_submission';
    protected $primaryKey = 'id_submission';
    public $timestamps = false;

    protected $fillable = [
        'id_tugas',
        'nim',
        'file_jawaban',
        'tgl_kumpul',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'tgl_kumpul' => 'datetime',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'id_tugas', 'id_tugas');
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'nim', 'nim');
    }
}

