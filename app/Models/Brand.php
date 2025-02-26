<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $guarded = ['id'] ;

    protected $appends = ['brand_image','product_count'];

    public function getBrandImageAttribute()
    {
        if(!empty($this->image)){
            return url($this->image);
           }
       
           return null;
    }

    public function getProductCountAttribute()
    {
 
    $count = Product::where('size_id', $this->id)
             ->where('status', 'active')
             ->where('is_approved', 1)
             ->count();

    return $count;
    }

}
