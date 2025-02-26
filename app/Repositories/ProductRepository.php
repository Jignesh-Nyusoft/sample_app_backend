<?php

namespace App\Repositories;
use App\Helpers\Helper;
use App\Models\CourierPartner;
use App\Models\Product;
use App\Models\ProductCourierPartner;
use App\Models\ProductImage;
use Auth;


class ProductRepository 
{

 public function StoreOrUopdate($request) {

  $image    = null;
  $approved = 1;
  $message ='Product Created Successfully.';

  if($request->hasFile('image') && !empty($request->file('image'))){
  
    $image = Helper::UploadImage($request->file('image'),'product');
  
  }elseif(!empty($request->product_id)){

  $product  =  Product::find($request->product_id);
  $image    =  $product->image ?? null;  
  $approved =  $product->is_approved;
  $message  = 'Product Updated Successfully.';  
  
  }
 
 $product =  Product::updateOrCreate(['id' => $request->product_id],[

    'product_name'  => $request->product_name,
    'slug'          => Helper::createSlug($request->product_name),
    'user_id'       => Auth::id(),
    'category_id'   => $request->category_id,
    'size_id'       => $request->size_id,
    'brand_id'      => $request->brand_id,
    'color_id'      => $request->color_id,
    'material_id'   => $request->material_id,
    'condition_id'  => $request->condition_id,
    'suitable_id'   => $request->suitable_id,
    'description'   => $request->description,
    'cloth_type'    => $request->cloth_type,
    'stock'         => $request->stock,
    'price'         => $request->price,
    'image'         => $image,
    'is_approved'   => 1,
    'status'        => 'active',

  ]);
  
  if(!empty($request->product_images) || isset($request->product_images) ){

    foreach($request->product_images as $images){
      
     $productimage = Helper::UploadImage($images,'product');

       ProductImage::create([
        'product_id'  => $product->id,
        'image'       => $productimage,      
     ]);  
     }

  }
 

  if (!empty($request->courier_partner)) {
    
    $newCourierPartnerIds = collect($request->courier_partner)->pluck('id')->toArray();

  
    $existingCourierPartners = ProductCourierPartner::where('product_id', $product->id)->get();

    foreach ($existingCourierPartners as $existing) {
        if (!in_array($existing->courier_partner_id, $newCourierPartnerIds)) {
            $existing->delete();
        }
    }

    foreach ($request->courier_partner as $list) {
        $courier = CourierPartner::find($list['id']);
    
        if ($courier) {
            $existingEntry = ProductCourierPartner::where('product_id', $product->id)
                ->where('courier_partner_id', $courier->id)
                ->first();
    
            if ($existingEntry) {

                if ($courier->name === 'USPS' && $existingEntry->size != $list['size']) {
                    $existingEntry->update([
                        'size' => $list['size'] ?? '',
                    ]);
                }
            } else {
             
                ProductCourierPartner::create([
                    'product_id' => $product->id,
                    'courier_partner_id' => $courier->id,
                    'courier_name' => $courier->name,
                    'size' => $list['size'] ?? '',
                ]);
            }
        }
    }
    
}


  return $message;
  }



}