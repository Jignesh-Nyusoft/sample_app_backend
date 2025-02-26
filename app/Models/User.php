<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'username',    
    'first_name',
    'last_name',
    'bio',
    'email',
    'password',
    'country_code',
    'mobile',
    'role',
    'status',
    'otp',
    'is_verify',
    'otp_valid_till',
    'is_otp_used',
    'zip_code',
    'is_online',
    'Stripe_connect_ac_id',
    'is_terms_agreed',
    'profile_image',
    'device_token',
    'is_seller',
    'stripe_customer_id',
    'remember_token',
    'gender',
    'is_seller_details_pending'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    protected $appends = ['unread_count','total_rating','user_image','my_earning','pickup_address_available'];

    public function getUserImageAttribute()
    {

        if(!empty($this->profile_image)){
            return url($this->profile_image);
        }
       
           return null;
    }
 
    public function getTotalRatingAttribute()
    {
        if (OrderReview::where('seller_id', $this->id)->exists()) {
            
            $data = OrderReview::where('seller_id', $this->id)
                ->selectRaw('AVG(rating) as rating, COUNT(*) as review_count')
                ->first();
    
            return [

                'rating'       => $data->rating,
                'review_count' => $data->review_count,
            ];
        }
    
        return [
            'rating'       => 0,
            'review_count' => 0,
        ];
    }


    public function getUnreadCountAttribute()
    {
        if (Notification::where('notifiable_id',$this->id)->exists()) {
            $data = Notification::where('notifiable_id',$this->id)->where('read_at',null)->count();
            return $data;
        }
    
          return 0;
    }


   public function Product(){
        
   return $this->hasMany(Product::class,'user_id')->where('is_approved',1);

   }


   public function pickpaddress(){
    
    return $this->hasOne(Address::class,'user_id')->where('is_default_pickup',1);

   }

   public function deliveryaddress(){
    
    return $this->hasOne(Address::class,'user_id')->where('is_default_delivery',1);

   }



   public function getMyEarningAttribute()
   {
       if ($this->is_seller == 1 && Order::where('seller_id',$this->id)->exists()) {
        $data = Order::where('seller_id', $this->id)
        ->where('payment_status', 'paid')
        ->where('delivery_status', 'delivered')
        ->sum('net_amount');
           
           return $data;
       }   
           return 0;
   }


   public function getPickupAddressAvailableAttribute()
   {
     $user = User::find($this->id);
    if ($user->is_seller == 1) {
        $pickupExists = Address::where('user_id', $this->id)
            ->where('is_default_pickup', 1)
            ->exists();
        
        if ($pickupExists) {
            return 1;
        }
    }
    return 0;

   }


}
