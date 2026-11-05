<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalasanDiskusi extends Model
{
    protected $table = 'tb_balasan_diskusi';
    protected $primaryKey = 'id_balasan';
    public $timestamps = false;

    protected $fillable = [
        'id_diskusi',
        'id_user',
        'isi_balasan',
        'tgl_balasan',
    ];

    protected $casts = [
        'tgl_balasan' => 'datetime',
    ];

    public function diskusi()
    {
        return $this->belongsTo(Diskusi::class, 'id_diskusi', 'id_diskusi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

