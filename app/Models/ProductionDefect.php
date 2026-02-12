<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDefect extends Model
{
    use HasFactory;

    protected $fillable = ['production_item_id', 'defect_type_id', 'qty', 'notes'];

    public function item()
    {
        return $this->belongsTo(ProductionItem::class, 'production_item_id');
    }

    public function defectType()
    {
        return $this->belongsTo(DefectType::class, 'defect_type_id');
    }
}
