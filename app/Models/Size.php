<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
use HasFactory;
protected $guarded = ['id'];

protected $appends = ['product_count'];

public function getProductCountAttribute()
{
 
    $count = Product::where('size_id', $this->id)
             ->where('status', 'active')
             ->where('is_approved', 1)
             ->count();

    return $count;
}

}
