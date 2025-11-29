<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Esim extends Model
{
    use HasFactory;

    protected $fillable = [
        'esim_plan_id',
        'phone_number',
        'assigned_to',
    ];

    public function plan()
    {
        return $this->belongsTo(EsimPlan::class, 'esim_plan_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
