<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;
    protected $table = 'movements';
    protected $fillable = [
        'source',
        'description',
        'status',
        'user_created_at',
        'user_updated_at',
    ];

    public function movementDetails()
    {
        return $this->hasMany(MovementDetail::class, 'movement_id');
    }
}
