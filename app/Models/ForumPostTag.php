<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPostTag extends Model
{
    protected $table = 'tb_forum_post_tag';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_post',
        'id_tag',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'id_post', 'id_post');
    }

    public function tag()
    {
        return $this->belongsTo(ForumTag::class, 'id_tag', 'id_tag');
    }
}

