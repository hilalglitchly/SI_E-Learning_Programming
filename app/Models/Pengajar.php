<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajar extends Model
{
    protected $table = 'tb_pengajar';
    protected $primaryKey = 'nidn';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nidn',
        'nama_pengajar',
        'email',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'nidn', 'nidn');
    }
}

