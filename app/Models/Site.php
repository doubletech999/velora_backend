<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'type',
        'image_url',
        'price',
        'guide_name',
        'guide_id',
        'distance',
        'duration',
        'activities'
    ];

    protected $casts = [
        'activities' => 'array',
        'price' => 'decimal:2',
        'distance' => 'decimal:2',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}