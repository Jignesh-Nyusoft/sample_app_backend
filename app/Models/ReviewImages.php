<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImages extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['review_image'];

    public function getReviewImageAttribute()
    {
        if (!empty($this->image)) {
            return url($this->image);
        }

        return null;
    }



}
