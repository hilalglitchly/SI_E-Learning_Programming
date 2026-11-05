<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumVote extends Model
{
    protected $table = 'tb_forum_vote';
    protected $primaryKey = 'id_vote';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_post',
        'id_jawaban',
        'tipe_vote',
        'tgl_vote',
    ];

    protected $casts = [
        'tgl_vote' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_post', 'id_post');
    }

    public function jawaban()
    {
        return $this->belongsTo(ForumJawaban::class, 'id_jawaban', 'id_jawaban');
    }
}

