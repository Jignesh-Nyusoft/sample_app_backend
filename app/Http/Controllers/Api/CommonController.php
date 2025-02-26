<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Helpers\StripeHelper;
use App\Helpers\TwilioHelper;
use App\Helpers\UberConnectHelper;
use App\Helpers\USPSHelper;
use App\Http\Controllers\Controller;
use App\Mail\ContactUsMail;
use App\Models\Address;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CMS;
use App\Models\Contactus;
use App\Models\Coupon;
use App\Models\CourierPartner;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Suitable;
use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Notifications\OrderNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Mail;
use Notification;
use Stripe\OAuth;
use Stripe\Stripe;


/**
 * @group Banner
 *
 * APIs for Banner
 */
class CommonController extends Controller
{

  public function getSuitableList(){
    $data = Suitable::where('status', 'active')->get(); 

    if (isset($data)) {
        foreach ($data as $suitable) {
            $suitablecat = Product::where('status', 'active')
                ->where('suitable_id', $suitable->id)
                ->whereHas('category', function($query) {
                    $query->where('status', 'active');
                })
                ->with('category:id,parent_id,name,slug,image')
                ->get()
                ->pluck('category')
                ->flatten()
                ->unique('id')
                ->values();            
            $category = [];
            
            foreach ($suitablecat as $maincat) {
                if ($maincat->parent_id != null) {
                    $category[] = $maincat->parent_id;
                }
            }

            if (!empty($category)) {
                $suitable['category'] = Category::whereIn('id', $category)->get();
            } else {
                $suitable['category'] = [];
            }
            }
    }

    return Helper::ApiResponse(200, 'Getting Suitable Data Successfully', $data);

}



public function getSuitable(Request $request){
  
$data = Suitable::where('status','active')->get();
   
return Helper::ApiResponse(200,'Getting Suiatable data Successfully',$data);
  
}



 /**
 * Get-Banner-List
 *
 * @header Content-Type application/JsonResponse
 * @header Authorization {access-token}
 *  @response {
 *    "status": 200,
 *    "message": "Getting Banner data Successfully",
 *    "data": [
 *        {
 *            "id": 1,
 *            "name": "End of Season Sale",
 *            "slug": "end-of-season-sale",
 *            "description": null,
 *            "image": "banner/3Ozs68eWjL.png",
 *            "status": "active",
 *            "created_at": "2024-08-22T06:26:00.000000Z",
 *            "updated_at": "2024-08-22T06:26:00.000000Z"
 *        }
 *    ]
 *}
 * 
 */
public function getBanner(){
  
 $data = Banner::where('status','active')->get();
    
 return Helper::ApiResponse(200,'Getting Banner data Successfully',$data);
      
}


/**
 * CMs-By-Slug
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 * @urlParam id required The ID of the CMS-id.URL/get-cms/term-conditions
 * @response{
 *    "status": 200,
 *    "message": "Getting CMS Page Data Successfully",
 *    "data": {
 *        "id": 2,
 *        "title": "Term&Conditions",
 *        "slug": "term-conditions",
 *        "keywords": "Check Test",
 *        "content": "<h2>Term&amp;ConditionsTerm&amp;ConditionsTerm&amp;ConditionsTerm&amp;Conditions</h2>",
 *        "image": null,
 *        "status": "inactive",
 *        "created_at": "2024-08-07T07:02:18.000000Z",
 *        "updated_at": "2024-08-07T07:02:18.000000Z"
 *    }
 *}
 * 
 */
public function getCMS($slug){
  
if(CMS::where('slug',$slug)->exists()){

$data = CMS::where('slug',$slug)->first();

return Helper::ApiResponse(200,'Getting CMS Page Data Successfully',$data);
      
}

return Helper::ApiResponse(400,'CMs Data Not Found',null);      

}


/**
 * Apply a Coupon
 *
 * Apply a discount coupon to a product and calculate the discounted price.
 * 
 * @bodyParam coupon_code string required The coupon code to be applied. Example: SAVE20
 * @bodyParam product_id int required The ID of the product to which the coupon is applied. Example: 101
 * 
 * @response {
 *    "status": 200,
 *    "message": "Coupon Applied Successfully",
 *    "data": {
 *        "net_amount": 150,
 *        "discount_price": "50",
 *        "total_amount": 100
 *    }
 *}
 */

public function applyCouponold(Request $request){
    
     $request->validate([
     
      'product_id'           => 'required|exists:products,id',             
      //'courier_partner_id' => 'required|exists:,column'
   
     ]);
     
     $product         = Product::find($request->product_id);
     $dropoff_id      = $request->address_id;

     $courier_partner = CourierPartner::find(1); 

     if($courier_partner->name == 'Uber'){
      $delivery   = UberConnectHelper::createDeliveryQuote($request->product_id,$dropoff_id,auth()->user()->id);
     
      try {
        $shipping_fee = $delivery['fee'] / 100;
      } catch (\Throwable $th) {
        $shipping_fee = 0;
      }
      

     }elseif($courier_partner->name == 'USPS'){
     
      $shipping_fee = 0;

     } 

     if($request->coupon_code != null){
    
     $message = "Coupon Applied Successfully";     

     $request->validate([      

     'coupon_code' => 'required|exists:coupons,coupon_code', 
 
     ]);

      $coupon  = Coupon::where('coupon_code',$request->coupon_code)->first();

      if (Carbon::now()->lt(Carbon::parse($coupon->valid_from))){

        return Helper::ApiResponse(400, 'This Coupon will start on ' . Carbon::parse($coupon->valid_from)->format('d-m-Y'), null);
      
      }
      
      if(Carbon::now()->format('Y-m-d') > $coupon->valid_till){
        return Helper::ApiResponse(400,'This Coupon is Expried -'.$coupon->valid_till,null);

      }

     if($product->price < $coupon->min_amount){
        return Helper::ApiResponse(400,'Minimum Amount is to use this coupon is '.$coupon->min_amount,null);
    
      }
     
     if(Order::where('coupon_id',$coupon->id)->count() >=  $coupon->no_of_coupon){        
       
      return Helper::ApiResponse(400,'Number of Coupon limit Reached',null);
     
     }           
          
     $discountprice    =  $product->price - $coupon->discount_amount;
     $totalorderamount =  $product->price - $discountprice; 
     
     if(Helper::get_settings("service_fee_commission") > 0 ){
      
     $adminservicefeeamount = ($totalorderamount * Helper::get_settings("service_fee_commission")) / 100;
    
     }else{
     $adminservicefeeamount = 0;
     }

     $finalorderamount = $totalorderamount + $adminservicefeeamount; 
     
     $data = [
      'net_amount'       => Helper::formatPriceToFloat($product->price),
      'discount_price'   => Helper::formatPriceToFloat($coupon->discount_amount),        
      'total_amount'     => Helper::formatPriceToFloat($finalorderamount),  
      'shipping_charges' => $shipping_fee,
      'tax'              => Helper::get_settings("service_fee_commission"),
      'service_fee'      => Helper::formatPriceToFloat($adminservicefeeamount),    
    ];

    }else{

     //$shipping_fee = $delivery['fee'] / 100;
     $message = "Getting Order Summary Successfully";  
     $data = [
      'net_amount'       => Helper::formatPriceToFloat($product->price),
      'discount_price'   => 0,        
      'total_amount'     => Helper::formatPriceToFloat($product->price),  
      'shipping_charges' => $shipping_fee,
      'tax'              => Helper::get_settings("service_fee_commission"),  
      'service_fee'      => $adminservicefeeamount ?? 0,
      ];

    }     
     return Helper::ApiResponse(200,$message,$data);

} 


/**
 * Get Contact Us Details
 *
 * This endpoint retrieves the contact information and site details from the settings.
 *
 * @response 200 {
 *   "status": 200,
 *   "message": "getting Contact Us Details Successfully",
 *   "data": {
 *     "site_name": "Example Site",
 *     "admin_email": "admin@example.com",
 *     "customer_care_mobile_no": "+123456789",
 *     "customer_care_email": "support@example.com",
 *     "copyright_text": "© 2024 Example Site. All rights reserved."
 *   }
 * }
 */

public function contactUS(){

$data = [

'site_name'  => Helper::get_settings('site_name'),
'admin_email'=> Helper::get_settings('admin_email'), 
'customer_care_mobile_no'=> Helper::get_settings('customer_care_mobile_no'), 
'customer_care_email'=> Helper::get_settings('customer_care_email'), 
'copyright_text'=> Helper::get_settings('copyright_text'), 

];


$data = [

    'extra_information' => 'This is some extra  data that will be sent with the notification.',
];

return Helper::ApiResponse(200,'getting Contact Us Details Successfully',$data);

}


/**
 * Store Contact Us details.
 *
 * This endpoint allows users to submit their contact details and a message.
 * The information is stored in the database and sent to the admin.
 * 
 * @bodyParam phone string required The user's phone number.  Example: +1234567890
 * @bodyParam email string required The user's email address. Example: user@example.com
 * @bodyParam message string optional The message from the user. Example: I need help with my order.
 *
 * @response 200 {
 *  "status": 200,
 *  "message": "Details Sent to Admin Successfully",
 *  "data": {
 *    "id": 1,
 *    "phone": "+1234567890",
 *    "email": "user@example.com",
 *    "message": "I need help with my order",
 *    "created_at": "2024-09-09T12:00:00.000000Z",
 *    "updated_at": "2024-09-09T12:00:00.000000Z",
 *   }
 * }
 */

public function contactusStore(Request $request){
     
    $request->validate([

     'phone'       => 'required',
     'email'       => 'required',
     'first_name'  => 'required',
     'last_name'   => 'required',  
     'message'     => 'required',  
     'country_code'=> 'required'

    ]);

   $data = Contactus::create([
    
   'phone'     => $request->phone,
   'country_code'     => $request->country_code,
   'email'     => $request->email,
   'message'   => $request->message,  
   'first_name'=> $request->first_name,  
   'last_name' => $request->last_name,  

  ]);
  

  try {
   Helper::SendContactViaMail($data);
  } catch (\Throwable $th) {
    
  }

   return Helper::ApiResponse(200,'Details Sent to Admin Successfully',$data);

}

public function getCouponList(){
  
     $data = Coupon::where('valid_from', '<=', Carbon::now())
     ->where('valid_till', '>=', Carbon::now())
     ->where('status', 'active')
     ->paginate(10);

    return Helper::ApiResponse(200,'Getting Coupon List Successfully',$data);
}
    
public function getNotification(){

  $notification = \App\Models\Notification::where('notifiable_id', auth()->user()->id)
  ->orderBy('created_at', 'desc') 
  ->paginate(10);

  $unreadnotification =  \App\Models\Notification::where('notifiable_id',auth()->user()->id)->where('read_at',null)->count();
  $readnotification   =  \App\Models\Notification::where('notifiable_id',auth()->user()->id)->where('read_at','!=',null)->count();

  $data = [
    'data'        => $notification,
    'unreadcount' => $unreadnotification,
    'readcount'   => $readnotification, 
  ];

  return Helper::ApiResponse(200,'Getting Notification List Successfully',$data);

}

public function readNotification($id){

  // $user = User::find(1);
  // $notification = [
  // 'title'    => "Order Checkout",
  // 'body'     => "Order Checkout Sucessfully",
  // 'message'  => "Hello $user->first_name, your order number 21212 has been Checkout. Thank you for your purchase!",
  // 'sender_id'=> 2,
  // 'image_url'=>"http://127.0.0.1:8000/setting/ihtohslpd7.png", 
  // 'type'     => "Order_Checkout",
  // 'user_id'  => $user->id, 
  // ];
  // NotificationHelper::sendNotifications("dYXOryh13k6StCNWFZdNjv:APA91bEhwGslN6wpvym4WQ0KVgB666sFGB55cz-gcCi37aQeHAi7T65nBMy_HQG1w6RZqC3wtvJYjcwyD3l0oK95eBGlqFTbydnB7tkN1kHfW77zhP64eAf9BGZopoAWwDwsDVzpnYx8",$notification);
  // $user->notify(new OrderNotification($notification));
  // return  1;

  \App\Models\Notification::where('id',$id)->update([

    'read_at' => Carbon::now() 
  
  ]);
  return Helper::ApiResponse(200,'Notification Read Successfully',null);

}


public function deleteNotification($id){
  
try {

  \App\Models\Notification::where('id',$id)->delete();

} catch (\Throwable $th) {  

  return Helper::ApiResponse(200,'Notification Not  Deleted',null);

}
  return Helper::ApiResponse(200,'Notification Deleted Successfully',null);
}


public function chatImageStore(Request $request){
  
  $request->validate([
    'image' => 'required|mimes:png,jpg,jpeg|max:2048',
  ]);
  
  $image = Helper::UploadImage($request->file('image'),'chat');

  $data = [  
    'image' => asset($image)
  ];

  return Helper::ApiResponse(200,'Chat Image Stored Successfully',$data);

}




public function checkDeliveryAvailability(Request $request){
  
$request->validate([

'courier_partner_id' => 'required',
'delivery_address_id'=> 'required|exists:addresses,id', 
'product_id'         => 'required|exists:products,id'

]);
  
$courier = CourierPartner::find($request->courier_partner_id);

if($courier->name == "Uber"){
  
  try {
    $data = UberConnectHelper::createDeliveryQuote($request->product_id,$request->delivery_address_id,auth()->user()->id);
    $data['status'] = true;
    
  } catch (\Throwable $th) {
    $data['status'] = false;
  
  }

}elseif($courier->name == "USPS"){

  // try {
  //   $data = USPSHelper::createDeliveryQuote();
  //   $data['status'] = true;
  
  // } catch (\Throwable $th) {
  //   $data['status'] = false;  
  // }
}

return Helper::ApiResponse(200,'Availiblity',$data);

}

public function uberWebhook(Request $request)
{
    $data = $request->all();

    $details = OrderShipment::where('delivery_id', $data['delivery_id'] ?? null)->first();
    $order = Order::find($details->order_id);
    $user = User::find($order->user_id);
    $seller = User::find($order->seller_id);
    if ($details) {

        if ($order) {
            
           if (isset($data['data']) && isset($data['data']['status']) && $data['data']['status'] === "pickup_complete") {
             $notification = [

                'title'    => "Order Shipped Successfully",
                'body'     => "Order Shipped Successfully",
                'message'  => "Hello $user->first_name, your order number $order->order_number has been shipped. Thank you for your purchase!",
                'sender_id'=> $order->orderitem->seller_id,
                'image_url'=> $order->orderitem?->product?->product_image ?? null, 
                'type'     => "Order_payment",
                'user_id'  => $user->id, 
                'order_id' => $order->id,
            ];
          
            try {
                NotificationHelper::sendNotifications($user->device_token, $notification, []);
            } catch (\Throwable $th) {
                \Log::error("Notification error: " . $th->getMessage());
            
            }

            Order::where('id',$order->id)->update([
            'delivery_status' => 'shipped'
            ]);

            $user->notify(new OrderNotification($notification));

            if (OrderStatus::where('order_id', $details->order_id)->where('status', 'shipped')->doesntExist()) {
              OrderStatus::create([
                  'order_id'  => $details->order_id,
                  'status'    => 'shipped'
              ]);
          }

          }


            $deliveryStatus = $data['data']['status'] ?? $details->status;
            $details->update([
                'status'      => $deliveryStatus,
                'customer_id' => $data['customer_id'] ?? $details->customer_id,
                'batch_id'    => $data['batch_id'] ?? $details->batch_id,
                'delivery'    => json_encode($data['data'] ?? []),
            ]);

            if (isset($data['data']) && isset($data['data']['status']) && $data['data']['status'] === "delivered") {
                $order->update([
                    'delivery_status' => 'delivered'
                ]);

                OrderStatus::create([
                    'order_id'  => $details->order_id,
                    'status'    => 'delivered'
                ]);

                $notification = [

                  'title'    => "Order Delivered Successfully",
                  'body'     => "Order Delivered Successfully",
                  'message'  => "Hello $user->first_name, your order number $order->order_number has been delivered. Thank you for your purchase!",
                  'sender_id'=> $order->orderitem->seller_id,
                  'image_url'=> $order->orderitem?->product?->product_image ?? null, 
                  'type'     => "Order_payment",
                  'user_id'  => $user->id, 
                  'order_id' => $order->id,
              ];

                try {
                    NotificationHelper::sendNotifications($user->device_token, $notification, []);
                } catch (\Throwable $th) {
                    \Log::error("Notification error: " . $th->getMessage());
                }

                $user->notify(new OrderNotification($notification));


                  
               if($seller->Stripe_connect_ac_id != null){
                $totalAmount = $order->net_amount - $order->seller_tax_amount;
                StripeHelper::payConnectedAccount(
                  $seller->Stripe_connect_ac_id,
                    $totalAmount,
                    $user->first_name
                );
               }

              
            }

            return response()->json(['success' => true]);
        }
    }

    return response()->json(['success' => false, 'message' => 'No matching delivery found'], 404);
}

// public function uberWebhook(Request $request)
// {
//     $data = $request->all();

//     $details = OrderShipment::where('delivery_id', $data['delivery_id'])->first();
 
    

//     if ($details) {

//       Order::where('id',$details->order_id)->update([
//         'delivery_status' => 'shipped'
//       ]);

//       $order = Order::where('id',$details->order_id)->first();;
//       $user = User::find($order->user_id);
//       $notification = [
//         'title'    => "Order Shipped Successfully",
//         'body'     => "Order Shipped Successfully",
//         'message'  => "Hello $user->first_name, your order number $order->order_number has been shipped. Thank you for your purchase!",
//         'sender_id'=> $order->orderitem->seller_id,
//         'image_url'=> $order->orderitem?->product?->product_image ?? null, 
//         'type'     => "Order_payment",
//         'user_id'  => $user->id, 
//         'order_id' => $order->id,
     
//       ];
//       try {
//         NotificationHelper::sendNotifications($user->device_token,$notification,$data = []);
//       } catch (\Throwable $th) {
//         //throw $th;
//       }
//       $user->notify(new OrderNotification($notification));
      
//       if(OrderStatus::where('order_id', $details->order_id) ->where('status', 'shipped')->doesntExist()){
//         OrderStatus::create([
//           'order_id'  => $details->order_id,
//           'status'    => 'shipped'
//         ]);
//       }
//         $details->update([
//             'status'      => $data['data']['status'] ?? $details->status,
//             'customer_id' => $data['customer_id'] ?? $details->customer_id,
//             // 'delivery_id' => $data['id'] ?? $details->delivery_id,
//             'batch_id'    => $data['batch_id'] ?? $details->batch_id,
//             // 'amount'      => isset($data['data']['fee']) ? $data['data']['fee'] / 100 : $details->amount,
//             'delivery'    => json_encode($data['data']),
//         ]);

      
//         if (isset($data['data']['status']) && $data['data']['status'] === "delivered") {
           
//             $order = Order::find($details->order_id);
//             Order::where('id',$details->order_id)->update([
//               'delivery_status' => 'delivered'
//               ]);

//               $order->refresh();
//               OrderStatus::create([

//                 'order_id'  => $details->order_id,
//                 'status'    => 'delivered'
//               ]);

//               $notification = [
//                 'title'    => "Order Delivered Successfully",
//                 'body'     => "Order Delivered Successfully",
//                 'message'  => "Hello $user->first_name, your order number $order->order_number has been delivered. Thank you for your purchase!",
//                 'sender_id'=> $order->orderitem->seller_id,
//                 'image_url'=> $order->orderitem?->product?->product_image ?? null, 
//                 'type'     => "Order_payment",
//                 'user_id'  => $user->id, 
//                 'order_id' => $order->id,
             
//               ];
//               try {
//                 NotificationHelper::sendNotifications($user->device_token,$notification,$data = []);
//               } catch (\Throwable $th) {
//                 //throw $th;
//               }
//               $user->notify(new OrderNotification($notification));

//             if ($order) {
//                 $user = User::find($order->seller_id);
//                 if ($user && $user->stripe_connect_ac_id) {

//                     $totalAmount = $order->net_amount - $order->seller_tax_amount;
//                     StripeHelper::payConnectedAccount(
//                         $user->stripe_connect_ac_id,
//                         $totalAmount,
//                         $user->first_name
//                     );
//                 }
//             }
//         }

      
//         return response()->json(['success' => true]);
//     }

//     return response()->json(['success' => false, 'message' => 'No matching delivery found'], 404);
// }



public function handleConnectCallback(Request $request)
{

    $authCode = $request->query('code');  

    if(!$authCode){
        return response()->json(['error' => 'Authorization code not found'], 400);
    }

    try {

        Stripe::setApiKey("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");  
    
        $response = OAuth::token([
            'grant_type' => 'authorization_code',
            'code'       => $authCode,
        ]);
        
        $stripeAccountId = $response->stripe_user_id; 
        $data = StripeHelper::getAccountDetails($response->stripe_user_id);

        if(isset($data['data']) && $data['data']['details_submitted'] == true ){

          $is_details_fill = 0;

        }else{

          $is_details_fill = 1;
        }

        $user = User::find($request->state);
        $user->Stripe_connect_ac_id      = $stripeAccountId;
        $user->is_seller_details_pending = $is_details_fill; 
        $user->save();

       $data = [
            'status'     => true,
            'account_id' => $stripeAccountId,
       ];
        
       return response()->json('Stripe account connected successfully,Please close the browser!');

    } catch (\Exception $e) { 
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



public function CheckExistingStripeConnectAccount(Request $request){

  $request->validate([

  'stripe_account_id'  => 'required'
  
  ]);

  $data = StripeHelper::getAccountDetails($request->stripe_account_id);
  $user = User::where('id',auth()->user()->id)->first();  
  
  if(isset($data['data']) && $data['data']['details_submitted'] == true ){
    $user->update([
      #can be use in futhure process
      #'is_seller'                => 1,
      'Stripe_connect_ac_id'      => $request->stripe_account_id,
      'is_seller_details_pending' => 0,
  ]);

  return Helper::ApiResponse(200,'Your Seller registration is successful. You can now start selling products with the details you provided.',null);

  }elseif(isset($data['data']) && $data['data']['details_submitted'] == false){
 
  $user->update([
      #can be use in futhure process
      #'is_seller'                 => 1,
      'Stripe_connect_ac_id'       => $request->stripe_account_id,
      'is_seller_details_pending'  => 1,
  ]);

  return Helper::ApiResponse(200,'Your Stripe account is valid; however, some details are still pending. Please update your Stripe Connect information to proceed.',null);

  }else{


    return Helper::ApiResponse(400,"We couldn't locate your Stripe Connect account, or it may not be valid. Please check your account details and try again.",null);
}
}

public function checkDeliveryStatus($id){
  
  if(Order::where('id',$id)->doesntExist()){

    return Helper::ApiResponse(400,"Order not Found",null);
  }


  $orderdelivery = OrderShipment::where('order_id',$id)->first();

  $uber = UberConnectHelper::generateTrackingUrl($orderdelivery->shipment_order_id);
  return Helper::ApiResponse(200,"getting Delivery Status Successfully",$uber);
  if($orderdelivery->exists()){
    $data = $orderdelivery->first();

    if($data->shipment_order_id != null){
      // $uber = UberConnectHelper::trackDelivery("5e6ba919-1ade-46bc-87e1-2ab35ba2b96d");
      $uber = UberConnectHelper::generateTrackingUrl($orderdelivery->shipment_order_id);
      return Helper::ApiResponse(200,"getting Delivery Status Successfully",$uber);
   
    }

    return Helper::ApiResponse(400,"Delivery Created But not Proceed",null);
    
    
  }



return Helper::ApiResponse(400,"Uber Shipment Not Created",null);

}




public function applyCoupon(Request $request) {

  $request->validate([

    'product_id'          =>  'required|exists:products,id',               
    'courier_partner_id'  =>  'required',               
  
  ]);


  $data = Helper::OrderSuammaryCalculate($request->product_id,$request->address_id,$request->courier_partner_id,$request->coupon_code);
  

  if($data['status'] == false ){

  return Helper::ApiResponse($data['code'],$data['message'],null);

  }
  
  return Helper::ApiResponse(200,'getting  Order Summary Successfully',$data);

}




public function CourierpartnerList() {
  
  $data = CourierPartner::where('status','active')->select('id','name','status')->get();
 
  return Helper::ApiResponse(200,'Getting  Courier Partner Successfully',$data);

}


// public function HandelUSPSStatus(Request $request) {
  
//   $data = $request->all();
//   $details = OrderShipment::where('shipment_id', $data['TrackInfo']['ID'] ?? null)->first();
  
//   if ($details) {
//     $order = Order::find($details->order_id);
//     $user = User::find($order->user_id);
//     $seller = User::find($order->seller_id);

//       if ($order) {
    
//         if (
//           isset($data['TrackInfo']) &&
//           (
//               (isset($data['TrackInfo']['Status']) && $data['TrackInfo']['Status'] == "In Transit") ||
//               (isset($data['TrackInfo']['StatusCategory']) && $data['TrackInfo']['StatusCategory'] == "In Transit") ||
//               (isset($data['TrackInfo']['StatusCategory']) && $data['TrackInfo']['StatusCategory'] == "Accepted")
//           )
//       ) {
//           if($order->is_shipped == 0){
            

//           $notification = [

//               'title'    => "Order Shipped Successfully",
//               'body'     => "Order Shipped Successfully",
//               'message'  => "Hello $user->first_name, your order number $order->order_number has been shipped. Thank you for your purchase!",
//               'sender_id'=> $order->orderitem->seller_id,
//               'image_url'=> $order->orderitem?->product?->product_image ?? null, 
//               'type'     => "Order_payment",
//               'user_id'  => $user->id, 
//               'order_id' => $order->id,
//           ];
        
//           try {
//               NotificationHelper::sendNotifications($user->device_token, $notification, []);
//           } catch (\Throwable $th) {
//               \Log::error("Notification error: " . $th->getMessage());
          
//           }

//           Order::where('id',$order->id)->update([
//           'delivery_status' => 'shipped',
//           'is_shipped'      => 1,
//           ]);

//           $user->notify(new OrderNotification($notification));

//           if (OrderStatus::where('order_id', $details->order_id)->where('status', 'shipped')->doesntExist()) {
//             OrderStatus::create([
//                 'order_id'  => $details->order_id,
//                 'status'    => 'shipped'
//             ]);
//         }

//         }
//       }


//           // $deliveryStatus = $data['data']['status'] ?? $details->status;
//           // $details->update([
//           //     'status'      => $deliveryStatus,
//           // ]);

//           if (!empty($data['TrackInfo']['Status']) && $data['TrackInfo']['Status'] == "Delivered") {

//               $order->update([
//                   'delivery_status' => 'Delivered'
//               ]);

//               OrderStatus::create([
//                   'order_id'  => $details->order_id,
//                   'status'    => 'delivered'
//               ]);

//               $notification = [

//                 'title'    => "Order Delivered Successfully",
//                 'body'     => "Order Delivered Successfully",
//                 'message'  => "Hello $user->first_name, your order number $order->order_number has been delivered. Thank you for your purchase!",
//                 'sender_id'=> $order->orderitem->seller_id,
//                 'image_url'=> $order->orderitem?->product?->product_image ?? null, 
//                 'type'     => "Order_delivered",
//                 'user_id'  => $user->id, 
//                 'order_id' => $order->id,
//             ];

//               try {
//                   NotificationHelper::sendNotifications($user->device_token, $notification, []);
//               } catch (\Throwable $th) {
//                   \Log::error("Notification error: " . $th->getMessage());
//               }

//              $user->notify(new OrderNotification($notification));
                
//              if($seller->Stripe_connect_ac_id != null){
//               $totalAmount = $order->net_amount - $order->seller_tax_amount;
//               StripeHelper::payConnectedAccount(
//                 $seller->Stripe_connect_ac_id,
//                   $totalAmount,
//                   $user->first_name
//               );
//              }

            
//           }

//           return response()->json(['success' => true]);
      
//         }
//   }

//   return response()->json(['success' => false, 'message' => 'No matching delivery found'], 404);
// }

public function HandelUSPSStatus(Request $request) {
  $requestData = $request->all(); 



  // Decode payload if it's a JSON string
  $decodedPayload = json_decode($requestData['payload'] ?? '', true);
  
  if (!$decodedPayload || !isset($decodedPayload['TrackInfo'])) {
      return response()->json(['success' => false, 'message' => 'Invalid payload format'], 400);
  }

  $trackInfo = $decodedPayload['TrackInfo'];
  $trackingId = $trackInfo['ID'] ?? null;
  
  if (!$trackingId) {
      return response()->json(['success' => false, 'message' => 'Tracking ID is missing'], 400);
  }

  
  $details = DB::table('order_shipments')->where('shipment_id', $trackingId)->first();
  if (!$details) {
      return response()->json(['success' => false, 'message' => 'No matching delivery found'], 404);
  }
  
  $order = DB::table('orders')->where('id', $details->order_id)->first();
  if (!$order) {
      return response()->json(['success' => false, 'message' => 'Order not found'], 404);
  }

  $user = User::find($order->user_id);
  $seller = User::find($order->seller_id);

  // Check if the status is "In Transit" or "Accepted"
  $isInTransit = isset($trackInfo['Status']) && $trackInfo['Status'] === "In Transit";
  $isStatusCategoryInTransit = isset($trackInfo['StatusCategory']) && in_array($trackInfo['StatusCategory'], ["In Transit", "Accepted"]);

  if ($isInTransit || $isStatusCategoryInTransit) {
      if ($order->is_shipped == 0) {
          $notification = [
              'title'    => "Order Shipped Successfully",
              'body'     => "Order Shipped Successfully",
              'message'  => "Hello {$user->first_name}, your order number {$order->order_number} has been shipped. Thank you for your purchase!",
              'sender_id'=> optional($order->orderitem)->seller_id,
              'image_url'=> optional(optional($order->orderitem)->product)->product_image, 
              'type'     => "Order_payment",
              'user_id'  => $user->id, 
              'order_id' => $order->id,
          ];

          try {
              NotificationHelper::sendNotifications($user->device_token, $notification, []);
          } catch (\Throwable $th) {
              \Log::error("Notification error: " . $th->getMessage());
          }

          $order->update([
              'delivery_status' => 'shipped',
              'is_shipped'      => 1,
          ]);

          $user->notify(new OrderNotification($notification));

          if (!OrderStatus::where('order_id', $order->id)->where('status', 'shipped')->exists()) {
              OrderStatus::create([
                  'order_id'  => $order->id,
                  'status'    => 'shipped'
              ]);
          }
      }
  }

  // Check if the status is "Delivered"
  if (!empty($trackInfo['Status']) && $trackInfo['Status'] === "Delivered") {
      $order->update(['delivery_status' => 'Delivered']);

      OrderStatus::create([
          'order_id' => $order->id,
          'status'   => 'delivered'
      ]);

      $notification = [
          'title'    => "Order Delivered Successfully",
          'body'     => "Order Delivered Successfully",
          'message'  => "Hello {$user->first_name}, your order number {$order->order_number} has been delivered. Thank you for your purchase!",
          'sender_id'=> optional($order->orderitem)->seller_id,
          'image_url'=> optional(optional($order->orderitem)->product)->product_image, 
          'type'     => "Order_delivered",
          'user_id'  => $user->id, 
          'order_id' => $order->id,
      ];

      try {
          NotificationHelper::sendNotifications($user->device_token, $notification, []);
      } catch (\Throwable $th) {
          \Log::error("Notification error: " . $th->getMessage());
      }

      $user->notify(new OrderNotification($notification));

      // Stripe payment to seller
      if (!empty($seller->Stripe_connect_ac_id)) {
          $totalAmount = $order->net_amount - $order->seller_tax_amount;
          StripeHelper::payConnectedAccount(
              $seller->Stripe_connect_ac_id,
              $totalAmount,
              $user->first_name
          );
      }
  }

  return response()->json(['success' => true]);
}






}
