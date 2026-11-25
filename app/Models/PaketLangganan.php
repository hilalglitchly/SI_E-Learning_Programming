<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketLangganan extends Model
{
    protected $table = 'tb_paket_langganan';
    protected $primaryKey = 'id_paket';
    public $timestamps = false;

    protected $fillable = [
        'nama_paket',
        'durasi_hari',
        'harga',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'durasi_hari' => 'integer',
        'harga' => 'integer',
        'is_active' => 'boolean',
    ];
}
