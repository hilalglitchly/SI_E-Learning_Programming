<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskusi extends Model
{
    protected $table = 'tb_diskusi';
    protected $primaryKey = 'id_diskusi';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'id_user',
        'judul_diskusi',
        'isi_diskusi',
        'tgl_post',
    ];

    protected $casts = [
        'tgl_post' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function balasan()
    {
        return $this->hasMany(BalasanDiskusi::class, 'id_diskusi', 'id_diskusi')->orderBy('tgl_balasan', 'asc');
    }
}

