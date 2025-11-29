<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'esim_id',
        'quantity',
        'total_price',
        'status',  // if you added status
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function esim()
    {
        return $this->belongsTo(Esim::class);
    }
}
