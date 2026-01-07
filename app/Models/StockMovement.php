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
        'from_location_id',
        'to_location_id',
        'shelf_id',
        'moved_by',
        'type',
        'quantity',
        'reason',
        'notes',
        'status',
        'due_date',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
        'due_date' => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class)->nullable();
    }

    public function movedBy()
    {
        return $this->belongsTo(User::class, 'moved_by');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now());
    }

    protected $appends = ['days_overdue'];

    public function getDaysOverdueAttribute()
    {
        if ($this->due_date && $this->due_date->isPast()) {
            return (int) $this->due_date->diffInDays(now());
        }
        return 0;
    }
}
