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
        'note',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

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
