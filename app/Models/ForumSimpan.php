<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumSimpan extends Model
{
    protected $table = 'tb_forum_simpan';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null; // composite

    protected $fillable = [
        'id_user',
        'id_post',
        'tgl_simpan',
    ];

    protected $casts = [
        'tgl_simpan' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_post', 'id_post');
    }
}

