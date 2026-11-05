<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tb_tugas';
    protected $primaryKey = 'id_tugas';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'judul_tugas',
        'deskripsi',
        'file_tugas',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'id_tugas', 'id_tugas');
    }
}

