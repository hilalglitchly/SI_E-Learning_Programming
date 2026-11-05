<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'tb_notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'link',
        'is_read',
        'tgl_dibuat',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'tgl_dibuat' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

