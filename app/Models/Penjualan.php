<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $fillable = [
        'marketing_id',
        'transaction_number',
        'date',
        'cargo_fee',
        'total_balance',
        'grand_total',
    ];
    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'id');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'penjualan_id', 'id');
    }
}
