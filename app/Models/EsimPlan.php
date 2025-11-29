<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EsimPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'plan_name',
        'data',
        'description',
        'validity_days',
        'price',
        'quantity',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function esims()
    {
        return $this->hasMany(Esim::class);
    }
}
