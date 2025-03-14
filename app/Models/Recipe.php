<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'category_id',
    ];

    protected $with = ['category'];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => asset('storage/' . $value),
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
