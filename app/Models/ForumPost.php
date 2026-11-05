<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $table = 'tb_forum_post';
    protected $primaryKey = 'id_post';
    public $timestamps = false; // tgl_post & tgl_update managed manually or db

    protected $fillable = [
        'id_user',
        'judul',
        'konten',
        'tipe',
        'status',
        'views',
        'is_pinned',
        'is_announcement',
        'tgl_post',
        'tgl_update',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_announcement' => 'boolean',
        'tgl_post' => 'datetime',
        'tgl_update' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function tags()
    {
        return $this->belongsToMany(ForumTag::class, 'tb_forum_post_tag', 'id_post', 'id_tag');
    }

    public function jawaban()
    {
        return $this->hasMany(ForumJawaban::class, 'id_post', 'id_post')->orderBy('is_solusi', 'desc')->orderBy('tgl_jawaban', 'asc');
    }

    public function votes()
    {
        return $this->hasMany(ForumVote::class, 'id_post', 'id_post');
    }

    public function simpan()
    {
        return $this->hasMany(ForumSimpan::class, 'id_post', 'id_post');
    }

    public function media()
    {
        return $this->hasMany(ForumMedia::class, 'id_post', 'id_post');
    }

    public function getScoreAttribute(): int
    {
        $up = $this->votes()->where('tipe_vote', 'up')->count();
        $down = $this->votes()->where('tipe_vote', 'down')->count();
        return $up - $down;
    }
}

