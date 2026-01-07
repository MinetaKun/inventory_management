<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'description', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
