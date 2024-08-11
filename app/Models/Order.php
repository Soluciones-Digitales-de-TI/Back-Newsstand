<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->whereIn('status', [1, 2]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_products')->withPivot('cantidad');
    }

}
