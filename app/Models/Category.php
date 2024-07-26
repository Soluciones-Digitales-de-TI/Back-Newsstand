<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public function scopeActive(Builder $query): void
    {
        $query->where('state', 1);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->whereIn('state', [1, 2]);
    }

}
