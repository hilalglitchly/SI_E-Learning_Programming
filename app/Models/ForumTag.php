<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTag extends Model
{
    protected $table = 'tb_forum_tag';
    protected $primaryKey = 'id_tag';
    public $timestamps = false;

    protected $fillable = [
        'nama_tag',
        'warna',
        'icon',
    ];

    public function posts()
    {
        return $this->belongsToMany(ForumPost::class, 'tb_forum_post_tag', 'id_tag', 'id_post');
    }
}

