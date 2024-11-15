<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementDetail extends Model
{
    use HasFactory;
    protected $table = 'movement_detail';
    protected $fillable = [
        'movement_id',
        'material_id',
        'type',
        'quantity',
        'status'
    ];

    public function movement()
    {
        return $this->belongsTo(Movement::class,'movement_id')->select('id', 'source');
    }

    public function material()
    {
        return $this->belongsTo(Material::class,'material_id')->select('id', 'name');
    }
}
