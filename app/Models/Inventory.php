<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_name',
        'product_label',
        'sku_ref',
        'category_id',
        'weight',
        'cost_price',
        'added_by',
        'status',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'status' => 'string',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
