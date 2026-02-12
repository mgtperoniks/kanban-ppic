<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefectType extends Model
{
    use HasFactory;

    protected $fillable = ['department', 'name', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
