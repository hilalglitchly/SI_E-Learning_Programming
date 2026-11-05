<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumMedia extends Model
{
    protected $table = 'tb_forum_media';
    protected $primaryKey = 'id_media';
    public $timestamps = false;

    protected $fillable = [
        'id_post',
        'id_jawaban',
        'file_path',
        'tipe_file',
        'tgl_upload',
    ];

    protected $casts = [
        'tgl_upload' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_post', 'id_post');
    }

    public function jawaban()
    {
        return $this->belongsTo(ForumJawaban::class, 'id_jawaban', 'id_jawaban');
    }
}

