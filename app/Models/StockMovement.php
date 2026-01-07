<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'variant_id',
        'location_id',
        'shelf_id',
        'moved_by',
        'type',
        'quantity',
        'reason',
        'notes',
        'status',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class)->nullable();
    }

    public function movedBy()
    {
        return $this->belongsTo(User::class, 'moved_by');
    }
}
