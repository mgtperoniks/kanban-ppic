<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionItem extends Model
{
    protected $fillable = [
        'heat_number',
        'item_name',
        'qty_pcs',
        'weight_kg',
        'customer',
        'current_dept',
        'line_number',
        'dept_entry_at',
        'remarks',
    ];

    protected $casts = [
        'dept_entry_at' => 'datetime',
    ];

    public function getAgingDaysAttribute()
    {
        return $this->dept_entry_at->diffInDays(now());
    }

    public function getAgingColorAttribute()
    {
        $days = $this->aging_days;

        if ($days < 5) return 'green';
        if ($days <= 14) return 'yellow';
        if ($days <= 30) return 'orange';
        return 'red';
    }
}
