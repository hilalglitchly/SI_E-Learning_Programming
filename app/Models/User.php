<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    public function pengajar()
    {
        return $this->hasOne(Pengajar::class, 'id_user', 'id_user');
    }

    public function murid()
    {
        return $this->hasOne(Murid::class, 'id_user', 'id_user');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user', 'id_user');
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class, 'id_user', 'id_user');
    }

    public function forumJawaban()
    {
        return $this->hasMany(ForumJawaban::class, 'id_user', 'id_user');
    }

    public function forumSimpan()
    {
        return $this->hasMany(ForumSimpan::class, 'id_user', 'id_user');
    }

    public function forumVotes()
    {
        return $this->hasMany(ForumVote::class, 'id_user', 'id_user');
    }

    public function langganan()
    {
        return $this->hasMany(Langganan::class, 'id_user', 'id_user');
    }

    public function activeSubscription(): ?Langganan
    {
        return Langganan::where('id_user', $this->id_user)
            ->where('status', 'active')
            ->where('tgl_berakhir', '>', now())
            ->orderByDesc('tgl_berakhir')
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    public function isPengajar(): bool
    {
        return strtolower($this->role) === 'pengajar';
    }

    public function isMurid(): bool
    {
        return strtolower($this->role) === 'murid';
    }

    public function isPimpinan(): bool
    {
        return strtolower($this->role) === 'pimpinan';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isPengajar() && $this->pengajar) {
            return $this->pengajar->nama_pengajar;
        }
        if ($this->isMurid() && $this->murid) {
            return $this->murid->nama_murid;
        }
        return $this->username;
    }

    public function getEmailAddress(): ?string
    {
        if ($this->isMurid()) {
            return $this->murid?->email;
        }
        if ($this->isPengajar()) {
            return $this->pengajar?->email;
        }
        return null;
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }
}
