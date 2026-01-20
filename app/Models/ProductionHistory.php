<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionHistory extends Model
{
    protected $fillable = [
        'item_id',
        'from_dept',
        'to_dept',
        'line_number',
        'qty_pcs',
        'weight_kg',
        'moved_at',
        'remarks',
    ];

    protected $casts = [
        'moved_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(ProductionItem::class);
    }
}
