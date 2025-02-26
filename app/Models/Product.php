<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory,SoftDeletes;
 
 protected $guarded = ['id'];

 protected $appends = ['is_fresh','is_wishlist','product_image','is_sold'];


 public function getProductImageAttribute()
 {
     if(!empty($this->image)){
        
     return url($this->image);
    }

    return null;
}


 public function getIsFreshAttribute()
 {
     try {

         $tenDaysAgo = Carbon::now()->subDays(10)->startOfDay();
         return $this->created_at->greaterThanOrEqualTo($tenDaysAgo);

        } catch (Exception $e) {
         \Log::error('Error determining if product is fresh: ' . $e->getMessage());
         return false;

        }
 }


 public function getIsSoldAttribute()
 {
     
     $item = OrderItems::where('product_id', $this->id )->where('status','=','processing') ->first();
     
     $sold = false;
     $date = null;
     $rating = 0;
     $orderid = null;
 
     if ($item) {
         $sold = true;
         $date = $item->created_at;
         $rating = 0;
         $orderid = $item->order_id;
 
         $review = OrderReview::where('order_id', $item->order_id)->first();
         if ($review) {
            $sold = true;
            $date = $item->created_at;
            $rating = $review->rating;
            $orderid = $item->order_id;
         }
     }
 
     return [
         'is_sold'   => $sold,
         'sold_date' => $date,
         'rating'    => $rating,
         'order_id'  => $orderid,
     ];
 }
 


 public function getIsWishlistAttribute()
 {
 if(Wishlist::where('user_id',auth()->user()->id)->where('product_id',$this->id)->exists()){
    return true;
 }
 return false;        
 }


public function Category(){

return $this->belongsTo(Category::class);

}


public function User(){

return $this->belongsTo(User::class);

}


public function Seller(){

return $this->belongsTo(User::class,'user_id','id');
    
}

public function Brand(){

return $this->belongsTo(Brand::class);

}

public function Suitable(){

return $this->belongsTo(Suitable::class);
    
}

public function Size(){

return $this->belongsTo(Size::class);

}


public function Material(){
   
return $this->belongsTo(Material::class);

}


public function Condition(){

return $this->belongsTo(Condition::class);

}


public function Color(){
    
return $this->belongsTo(Color::class);

}


public function ProductImages() {

return $this->hasMany(ProductImage::class);

}

public function ProductCourier() {

    return $this->hasMany(ProductCourierPartner::class);
    
}





}



