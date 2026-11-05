<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumJawaban extends Model
{
    protected $table = 'tb_forum_jawaban';
    protected $primaryKey = 'id_jawaban';
    public $timestamps = false;

    protected $fillable = [
        'id_post',
        'id_user',
        'konten',
        'is_solusi',
        'tgl_jawaban',
    ];

    protected $casts = [
        'is_solusi' => 'boolean',
        'tgl_jawaban' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_post', 'id_post');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function votes()
    {
        return $this->hasMany(ForumVote::class, 'id_jawaban', 'id_jawaban');
    }

    public function media()
    {
        return $this->hasMany(ForumMedia::class, 'id_jawaban', 'id_jawaban');
    }

    public function getScoreAttribute(): int
    {
        $up = $this->votes()->where('tipe_vote', 'up')->count();
        $down = $this->votes()->where('tipe_vote', 'down')->count();
        return $up - $down;
    }
}

