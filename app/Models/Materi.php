<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'tb_materi';
    protected $primaryKey = 'id_materi';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'judul_materi',
        'deskripsi',
        'file_materi',
        'tgl_upload',
    ];

    protected $casts = [
        'tgl_upload' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}

