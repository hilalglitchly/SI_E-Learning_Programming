<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'tb_kelas';
    protected $primaryKey = 'id_kelas';
    public $timestamps = false;

    protected $fillable = [
        'nama_kelas',
        'deskripsi',
        'nidn',
    ];

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'nidn', 'nidn');
    }

    public function peserta()
    {
        return $this->belongsToMany(Murid::class, 'tb_peserta_kelas', 'id_kelas', 'nim');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'id_kelas', 'id_kelas');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_kelas', 'id_kelas');
    }

    public function kuis()
    {
        return $this->hasMany(Kuis::class, 'id_kelas', 'id_kelas');
    }

    public function diskusi()
    {
        return $this->hasMany(Diskusi::class, 'id_kelas', 'id_kelas')->orderBy('tgl_post', 'desc');
    }

    public function canManage(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPengajar()) {
            return Pengajar::where('id_user', $user->id_user)
                ->where('nidn', $this->nidn)
                ->exists();
        }

        return false;
    }

    public function canView(User $user): bool
    {
        if ($user->isAdmin() || $user->isPimpinan()) {
            return true;
        }

        if ($user->isPengajar()) {
            return $this->canManage($user);
        }

        if ($user->isMurid()) {
            $nim = Murid::where('id_user', $user->id_user)->value('nim');
            return $nim !== null && PesertaKelas::where('id_kelas', $this->id_kelas)
                ->where('nim', $nim)
                ->exists();
        }

        return false;
    }
}

