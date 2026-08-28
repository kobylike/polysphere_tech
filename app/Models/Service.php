<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'featured_image',
        'additional_images',
        'icon',
        'order',
        'status'
    ];

    protected $casts = [
        'additional_images' => 'array',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }
}
