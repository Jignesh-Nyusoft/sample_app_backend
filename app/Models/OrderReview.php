<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReview extends Model
{
    use HasFactory;

protected $guarded = ['id'];

public function ReviewImages(){
    
return $this->hasMany(ReviewImages::class,'review_id');


}

public function reviewGivenByUser()
{
    return $this->belongsTo(User::class, 'user_id');
}


public function seller(){
    
return $this->hasOne(User::class,'id','seller_id');
            
}


public function order(){
    
return $this->hasOne(Order::class,'id','order_id');
                
}


public function Product(){
    
    return $this->hasOne(Product::class,'id','product_id');
                    
}

}
