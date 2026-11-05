<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    protected $table = 'tb_murid';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nim',
        'nama_murid',
        'email',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'tb_peserta_kelas', 'nim', 'id_kelas');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'nim', 'nim');
    }

    public function nilaiKuis()
    {
        return $this->hasMany(NilaiKuis::class, 'nim', 'nim');
    }
}

