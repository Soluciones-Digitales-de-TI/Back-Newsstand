<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrdersProducts extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'product_id', 'cantidad'];

    public function order(){
        return $this->belongsTo(Order::class);
        
    }

    public function product(){
        return $this->belongsTo(Product::class);
        
    }
}
