<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Langganan extends Model
{
    protected $table = 'tb_langganan';
    protected $primaryKey = 'id_langganan';
    public $timestamps = false;

    protected $fillable = [
        'kode_invoice',
        'id_user',
        'id_paket',
        'nominal',
        'status',
        'snap_token',
        'payment_url',
        'payment_type',
        'gateway_transaction_id',
        'gateway_status',
        'catatan_admin',
        'tgl_mulai',
        'tgl_berakhir',
        'verified_at',
        'created_at',
    ];

    protected $casts = [
        'nominal' => 'integer',
        'tgl_mulai' => 'datetime',
        'tgl_berakhir' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        $clear = static function (self $sub): void {
            if (app()->bound('request')) {
                request()->attributes->remove('_active_sub_' . $sub->id_user);
            }
            Cache::forget('dashboard.pimpinan_stats.v1');
        };

        static::saved($clear);
        static::deleted($clear);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function paket()
    {
        return $this->belongsTo(PaketLangganan::class, 'id_paket', 'id_paket');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }

    public function isCurrentlyActive(): bool
    {
        return $this->status === 'active'
            && $this->tgl_berakhir !== null
            && $this->tgl_berakhir->isFuture();
    }
}
