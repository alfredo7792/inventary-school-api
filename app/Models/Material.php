<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $table = 'materials';

    protected $fillable = [
        'id',
        'name',
        'description',
        'stock',
        'image',
        'status',
        'category_id',
        'user_created_at',
        'user_updated_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->select('id', 'name');
    }

    public function detail_movements()
    {
        return $this->hasMany(MovementDetail::class, 'material_id');
    }
}
