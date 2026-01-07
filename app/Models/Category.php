<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
