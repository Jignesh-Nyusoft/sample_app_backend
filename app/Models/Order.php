<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected  $guarded = ['id'];
    protected $attributes = [
        'order_number' => null,
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order_number = $model->generateUniqueOrderNumber();
        });
    }

   
    public function generateUniqueOrderNumber()
    {
        do {
            $orderNumber = substr(str_shuffle(str_repeat('0123456789', 9)), 0, 10);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }


    public function orderItem()
    {
        return $this->hasOne(OrderItems::class, 'order_id');
    }


    public function user()
    {
        return $this->hasOne(user::class, 'id','user_id');
    }

    public function seller()
    {
        return $this->hasOne(user::class, 'id','user_id');
    }

      
    public function deliveryaddress()
    {
        return $this->hasOne(Address::class, 'id','address_id');
    }


    public function Orderstatus()
    {
        return $this->hasMany(OrderStatus::class, 'order_id','id');
    }

    public function review()
    {
        return $this->hasOne(OrderReview::class, 'order_id','id');
    }

    public function orderShipment()
    {
        return $this->hasOne(OrderShipment::class, 'order_id');
    }

    public function getPaymentMethodAttribute($value)
    {
        return ucwords(strtolower($value));
    }
}
