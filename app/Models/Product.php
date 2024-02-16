<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'category_id',
        'seller_id',
    ];

    protected $appends = [
        'rupiah'
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/' . $value),
        );
    }

    protected function rupiah(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $data) =>  'Rp. ' . number_format($data['price']),
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
