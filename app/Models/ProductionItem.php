<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionItem extends Model
{
    protected $fillable = [
        'plan_id',
        'code',
        'item_code',
        'heat_number',
        'item_name',
        'aisi',
        'size',
        'qty_pcs',
        'scrap_qty',
        'weight_kg',
        'bruto_weight',
        'netto_weight',
        'bubut_weight',
        'finish_weight',
        'po_number',
        'customer',
        'current_dept',
        'line_number',
        'dept_entry_at',
        'production_date',
        'remarks',
    ];

    protected $casts = [
        'dept_entry_at' => 'datetime',
        'production_date' => 'date',
        'weight_kg' => 'decimal:2',
        'bruto_weight' => 'decimal:2',
        'netto_weight' => 'decimal:2',
        'bubut_weight' => 'decimal:2',
        'finish_weight' => 'decimal:2',
        'scrap_qty' => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(ProductionPlan::class, 'plan_id');
    }

    public function getAgingDaysAttribute()
    {
        return $this->dept_entry_at->diffInDays(now());
    }

    public function getAgingColorAttribute()
    {
        $days = $this->aging_days;

        if ($days < 5)
            return 'green';
        if ($days <= 14)
            return 'yellow';
        if ($days <= 30)
            return 'orange';
        return 'red';
    }
}
