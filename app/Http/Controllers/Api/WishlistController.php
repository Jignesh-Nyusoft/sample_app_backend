<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Auth;
use Illuminate\Http\Request;
  
/**
   * @group Wishlist
   *
   * APIs for Add Product in wishlist,Remove product from wishlist and get wishlist data
*/
class WishlistController extends Controller
{


/**
 * Get-Wishlist
 *
 * @header Content-Type application/JsonResponse
 * @header Authorization {access-token}
 *  @response {
 *    "status": 200,
 *    "message": "Getting Wishlist Data Successfully",
 *    "data": [
 *        {
 *            "id": 2,
 *            "product_id": 2,
 *            "user_id": 1,
 *            "created_at": null,
 *            "updated_at": null,
 *            "product": [
 *                {
 *                    "id": 2,
 *                    "product_name": "earum",
 *                    "description": "Nisi voluptas aperiam et veniam repellendus. Est suscipit quos velit voluptatum. Sint consequuntur et earum quis alias. Excepturi dignissimos perferendis eos dolore enim sunt.",
 *                    "cloth_type": "old",
 *                    "stock": 31,
 *                    "price": 352.75,
 *                    "image": "https://via.placeholder.com/640x480.png/007744?text=itaque",
 *                    "product_image": "https://via.placeholder.com/640x480.png/007744?text=itaque"
 *                }
 *            ]
 *        }
 *    ]
 *}
 * 
 */
    public function getWishlist(){
        
    $data = Wishlist::with('Product')->where('user_id',Auth::id())->orderBy("id","desc")->get();
    return Helper::ApiResponse(200,"Getting Wishlist Data Successfully",$data);
    
   }

 /**
 * Add Product into Wishlist
 *
 * @header Content-Type application/JsonResponse
 * @header Authorization {access-token}
 * @bodyParam product_id   Product Id required Example: 1
 *  @response {
 *    "status": 200,
 *    "message": "Added to Wishlist Successfully",
 *    "data": {
 *        "user_id": 1,
 *        "product_id": "3",
 *        "updated_at": "2024-08-13T09:59:00.000000Z",
 *        "created_at": "2024-08-13T09:59:00.000000Z",
 *        "id": 5
 *    }
 *}
 * 
 */ 
   public function addToWishlist(Request $request) {
        
    $request->validate([

    'product_id' =>  'required|exists:products,id'    
    
    ]);    

    if(Wishlist::where(['user_id' => Auth::id(),'product_id' => $request->product_id])->exists()){

    return Helper::ApiResponse(400,'This Product is Already in wishlist',null);
    }

    $data = Wishlist::create([

     'user_id'    => Auth::id(),
     'product_id' => $request->product_id,
     
    ]);

    return Helper::ApiResponse(200,'Added to Wishlist Successfully',$data);

    }

/**
 * Remove Product From Wishlist
 *
 * @header Content-Type application/JsonResponse
 * @header Authorization {access-token}
 * @urlParam id required The ID of the Wishlist-id.URL/remove-wishlist/1
 *  @response {
 *    "status": 200,
 *    "message": "Successfully Removed From Wishlist",
 *    "data": null
 *}
 * 
 */   
   public function removeWishlist($id){
        
   if(Wishlist::where('user_id',Auth::id())->where('product_id',$id)->exists()){

   $wishlist = Wishlist::where('user_id',Auth::id())->where('product_id',$id)->delete();
   return Helper::ApiResponse(200,'Removed From Wishlist',null);

   }else{
    
   return Helper::ApiResponse(400,'Data Not Found Please try again!',null);

   }

   }
}
