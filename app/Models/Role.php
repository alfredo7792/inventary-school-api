<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'status',
        'user_created_at',
        'user_updated_at'
    ];

    public function users(){
        $this->belongsToMany(User::class);
    }
}
