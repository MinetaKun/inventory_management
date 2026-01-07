<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inventory_id',
        'sku',
        'image',
        'quantity',
        'min_stock',
        'note',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'min_stock' => 'integer',
        'quantity' => 'integer',
    ];

    protected $appends = ['is_low_stock'];

    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->min_stock;
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values'
        )->withPivot('attribute_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
