<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Helpers\NotificationHelper;
use App\Helpers\StripeHelper;
use App\Helpers\UberConnectHelper;
use App\Helpers\USPSHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Coupon;
use App\Models\CourierPartner;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderPayment;
use App\Models\OrderShipment;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderNotification;
use Auth;
use Illuminate\Http\Request;
use Log;
use Str;

class OrderController extends Controller
{
 
  
/**
 * Create a New Order
 *
 * Create a new order for a product, including order items and calculate the total amount.
 * 
 * @bodyParam address_id int required The ID of the address where the order will be delivered. Example: 5
 * @bodyParam product_id int required The ID of the product being ordered. Example: 101
 * @bodyParam discount_price float The discount applied to the order. Example: 10.00
 * @bodyParam coupon_id int The ID of the coupon applied to the order, if any. Example: 2
 * @bodyParam delivery_charge float required The delivery charge for the order. Example: 5.00
 * 
 * @response 200 {
 *   "status": 200,
 *   "message": "Order Created Successfully",
 *   "data": {
 *     "id": 1,
 *     "address_id": 5,
 *     "user_id": 1,
 *     "seller_id": 3,
 *     "net_amount": 100.00,
 *     "discount": 10.00,
 *     "coupon_id": 2,
 *     "total_amount": 100.00,
 *     "delivery_charge": 5.00,
 *     "delivery_status": "pending",
 *     "created_at": "2024-08-28T12:34:56.000000Z",
 *     "updated_at": "2024-08-28T12:34:56.000000Z"
 *   }
 * }
 */
public  function orderCreate(OrderRequest $request){

 $product = Product::find($request->product_id);
 $offline = 0;

 if(OrderItems::where('product_id',$product->id)->where('status','!=','pending')->exists()){

 return  Helper::ApiResponse(400,'Product Already Sold Please try other Products',null);

}


$courierpartner = CourierPartner::find($request->courier_partner_id);

if($courierpartner->name == "Uber"){

 $delivery = UberConnectHelper::createDeliveryQuote($request->product_id,$request->address_id,auth()->user()->id);

 if(isset($delivery['stat']) && $delivery['stat'] == false){

  return  Helper::ApiResponse(400,$delivery['message'],null);

}else{

  $quote_id = $delivery['id'];  
}

}elseif($courierpartner->name == "USPS"){

  $quote_id = null;
  
}else{

  $quote_id = null;
 $offline = 1; 
}


if($request->coupon_id != null){

  $coupon = Coupon::find($request->coupon_id);
  $coupon_code = $coupon->coupon_code;

}else{
  $coupon_code = null;

}
$data = Helper::OrderSuammaryCalculate($request->product_id,$request->address_id,$request->courier_partner_id,$coupon_code);

if($data == false ){
  return Helper::ApiResponse(200,'Your address is not deliverable by Uber. Please check and update the address.',null);

}


$order =  Order::create([
  
   'address_id'         => $request->address_id,
   'user_id'            => auth()->user()->id,
   'seller_id'          => $product->user_id,
   'net_amount'         => $request->net_amount,
   'discount_price'     => $request->discount_price,
   'coupon_id'          => $request->coupon_id,
   'total_amount'       => $request->total_amount,
   'delivery_charge'    => $request->shipping_charges,
   'delivery_status'    => 'pending', 
   'is_review'          => 0,
   'service_fee'        => $request->service_fee,
   'buyer_tax'          => Helper::get_settings('service_fee_commission'),
   'seller_tax'         => 0,
   'seller_tax_amount'  => 0,  
   'stripe_platform_fee'=> $request->stripe_platform_fee,
   'sales_tax_amount'   => $request->sales_tax_amount,
   'is_offline_delivery'=> $offline
]);

  OrderItems::create([
    'order_id'          => $order->id,
    'product_id'        => $request->product_id,
    'user_id'           => auth()->user()->id,
    'seller_id'         => $product->user_id, 
    'product_price'     => $product->price,
    'delivery_charge'   => $request->shipping_charges, 
    'total_amount'      => $request->total_amount,
    'quantity'          => '1',     
    'status'            => 'pending',
    'service_fee'       => $request->service_fee,
    'buyer_tax'         => Helper::get_settings('service_fee_commission'),
    'seller_tax'        =>  0,  
    'payment_method'    => 'card',

  ]);

 Helper::OrderStatusCreate($order,'pending');


 OrderShipment::create([
  'order_id'           =>  $order->id,
//'quote_id' =>  $delivery['id'],
  'quote_id'           =>  $quote_id,
  'quotation'          =>  null,
  'status'             =>  'pending',
  'currency'           =>  'usd',
  'courier_partner_id' => $request->courier_partner_id,
]);

 try {

 } catch (\Throwable $th) {
  
Log::info('shipment is not created for this order',[$th]);

}


 try {
  
  $intent = StripeHelper::createPaymentIntent($order->total_amount,auth()->user()->stripe_customer_id);
  $EphemeralKeys = StripeHelper::createEphemeralKeys(auth()->user()->stripe_customer_id);      

  OrderPayment::create([  
   'user_id'           => auth()->user()->id,
   'order_id'          => $order->id,
   'stripe_intent_id'  => $intent->id,
   'stripe_customer_id'=> auth()->user()->stripe_customer_id, 
   'payment_method'    => 'Card',
   'payment_method_id' => null,
   'amount'            => $order->total_amount,
   'net_amount'            => $order->net_amount,
   'response'          => json_encode($intent),
 ]);

 $order['stripe_intent_id'] = $intent->id;
 $order['payment_intent_secret_id'] = $intent->client_secret;
 $order['ephemeral_key_secret_id']  = $EphemeralKeys['secret'];
 $order['ephemeral_key_id']   = $EphemeralKeys['id'];
 $order['stripe_customer_id'] = auth()->user()->stripe_customer_id;

} catch (\Throwable $th) {
  $order['stripe_intent_id'] = null;
  $order['payment_intent_secret_id'] = null;
  $order['ephemeral_key_secret_id'] = null;
  $order['ephemeral_key_id'] = null;
  $order['stripe_customer_id'] = auth()->user()->stripe_customer_id;
 
}

return  Helper::ApiResponse(200,'Order Created Successfully',$order);

}



public function orderCheckout(Request $request){

   $request->validate([
    'order_intent_id' => 'required',
    'order_id'        => 'required|exists:orders,id'

   ]);

   $order = Order::with('orderItem.product')->where('id',$request->order_id)->first(); 
   $order->update([
    'delivery_status' => 'processing',
   ]);

    OrderItems::where('order_id',$order->id)->update([
  
      'status'  => 'processing',
    
    ]);
     
     $paytype ="Card";

     if($request->type == 'applepay'){

      $paytype = 'applepay';

     }

    OrderPayment::where('order_id',$order->id)->Update([

    'payment_method' => $paytype, 
     
    ]);

    $user   = User::find($order->user_id);
    $seller = User::find($order->seller_id);

    $notification = [     

     'title'    => "Order Checkout",
     'body'     => "Order Checkout Sucessfully",
     'message'  => "Hello $user->first_name, your order number $order->order_number has been Checkout. Thank you for your purchase!",
     'sender_id'=> $order->orderitem->seller_id,
     'image_url'=> $order->orderItem?->product?->product_image ?? null, 
     'type'     => "Order_Checkout",
     'user_id'  => $user->id, 
     'order_id' => $order->id, 
    
    ];

    $sellernotification = [
      'title'    => "Product sold out",
      'body'     => "Product sold out",
      'message'  => "Hello $seller->first_name, you have a new order for your product!order number $order->order_number, Please check the details and prepare for fulfillment.",
      'sender_id'=> $order->orderitem->seller_id,
      'image_url'=> $order->orderItem?->product?->product_image ?? null, 
      'type'     => "Order_Checkout",
      'user_id'  => $seller->id, 
      'order_id' => $order->id, 
     
    ];

    $name    = $user->first_name;
    $email   = $user->email;
    $subject = "Order Placed Successfully";
    $mailData['SUBJECT'] = $subject;
    $mailData['EMAIL']   = $email;
    $mailData['NAME']    = $name;
    $mailData['LINK']    = "";
    $mailData['ORDER_NUMBER'] = $order->order_number;
    $mailData['ORDER_AMOUNT'] = $order->total_amount;
    $new_email_data = Helper::CreateMailTemplate("19", $mailData, $subject);
    $new_subject    = $subject;
    $new_content    = $new_email_data[1];
    $new_fromdata   = ['email' => $email,'name' => $name];
    $new_mailids    = [$email  => $name];
    $product = Product::where('id',$order->orderitem->product->id)->first();

    $product->update([
      'stock'  => 0,
      'status' => 'inactive' 
    ]);


    NotificationHelper::sendNotifications(auth()->user()->device_token,$notification,$data = []);
    NotificationHelper::sendNotifications($seller->device_token,$sellernotification,$data = []);
    $user->notify(new OrderNotification($notification));
    $seller->notify(new OrderNotification($sellernotification));
    Helper::SendMailWithTemplate($new_fromdata, $new_subject, $new_content, $new_mailids); 
    Helper::SendProductSoldOutMail($order->id);   
    
    return  Helper::ApiResponse(200,'Order Checkout Successfully',null);
  
}


/**
 * Get Purchase Order List
 *
 * Retrieve a list of purchase orders based on their delivery status. The status can be 'pending', 'shipped', or 'delivered'.
 * 
 * @urlParam status string The delivery status of the orders to be retrieved. Example: shipped
 * 
 * @response 200 {
 *   "status": 200,
 *   "message": "Getting Orders list Successfully",
 *   "data": [
 *     {
 *       "id": 1,
 *       "order_number": "ORD123456",
 *       "delivery_status": "shipped",
 *       "orderitem": {
 *         "product": {
 *           "id": 101,
 *           "product_name": "Product 1",
 *           "price": 100.00,
 *           "image": "https://example.com/images/product1.jpg",
 *           "is_approved": 1,
 *           "created_at": "2024-08-28T12:34:56.000000Z"
 *         }
 *       }
 *     }
 *   ]
 * }
 */

public function purchaseOrderList($status = null){
  
if($status == 'shipped'){

 $status = 'shipped';

}elseif($status == 'delivered'){

 $status = 'delivered';   

}elseif($status == 'processing'){

 $status = 'processing';
 
}else{

 $status = 'pending';
}    
    
$order = Order::where('delivery_status', $status)
          ->with(['Orderstatus','review.ReviewGivenByUser','review.ReviewImages','orderShipment','orderitem.product' => function ($query) {
              $query->select(
                  'id',
                  'size_id',
                  'product_name', 
                  'price', 
                  'image', 
                  'is_approved', 
                  'created_at', 
  );
}])->where('user_id',auth()->user()->id) ->orderBy('id','desc')->get(['id','order_number','delivery_status']);  
        
return  Helper::ApiResponse(200,'Getting Orders list Successfully',$order);

}


/**
 * Get Order Details by ID
 *
 * Retrieves the details of a specific order by its ID, including order items and their associated products, as well as the order status.
 *
 * @urlParam id int required The ID of the order. Example: 123
 * 
 * @header Authorization Bearer token The access token of the authenticated user.
 *
 * @response 200 {
 *   "status": 200,
 *   "message": "Getting Order details Successfully",
 *   "data": {
 *     "id": 123,
 *     "user_id": 1,
 *     "total": 150.75,
 *     "status": "delivered",
 *     "created_at": "2023-09-10 12:30:00",
 *     "updated_at": "2023-09-10 12:30:00",
 *     "order_items": [
 *       {
 *         "id": 1,
 *         "order_id": 123,
 *         "product_id": 456,
 *         "quantity": 2,
 *         "price": 50.25,
 *         "product": {
 *           "id": 456,
 *           "name": "Product Name",
 *           "description": "Product description",
 *           "price": 50.25
 *         }
 *       }
 *     ],
 *     "orderstatus": {
 *       "id": 1,
 *       "name": "Delivered"
 *     }
 *   }
 * }
 */
public function orderById($id = null){


  
  $order = Order::with([
    'orderItem.product',
    'Orderstatus',
    'deliveryaddress',
    'review.ReviewGivenByUser',
    'review.ReviewImages',
    'orderShipment' => function ($query) {
        $query->select([
            'id', 
            'order_id', 
            'quote_id', 
            'delivery_id', 
            'customer_id', 
            'batch_id', 
            'amount', 
            'status', 
            'tracking_url', 
            'courier_partner_id'
        ])->with('courierPartner:id,name');
    }
])->find($id);

  return Helper::ApiResponse(200, 'Getting Order details Successfully', $order);

}

public function orderPaymentWebhook(Request $request){
  
  $data = $request->all();
  
  if ($data['type'] == 'payment_intent.succeeded') {
      $paymentIntent = $data['data']['object'];
      $response = [

          'payment_intent_id' => $paymentIntent['id'],
          'amount'   => $paymentIntent['amount'],
          'currency' => $paymentIntent['currency'],
          'status'   => $paymentIntent['status'],
      
        ];
      
      $id = $paymentIntent['id'];
      $payment   = OrderPayment::where('stripe_intent_id',$paymentIntent['id'])->first();
      $order     = Order::where('id',$payment->order_id)->first();
      $orderitem = OrderItems::with('Product')->where('order_id',$order->id)->first();
      
      $order->update([

        'payment_status' => 'paid',
        'delivery_status'=> 'processing'

      ]);
    
      $payment->update([
        
      'status' => 'Paid',
      
      ]);

      $courier_partner = OrderShipment::where('order_id',$order->id)->first();
      $courier = CourierPartner::find($courier_partner->courier_partner_id);

      if($order->is_offline_delivery == 1){
           
              
      }elseif($courier->name == 'Uber'){
        $delivery = UberConnectHelper::createDelivery($orderitem->product->id,$order->address_id,$order->user_id,$order->id);

        if(isset($delivery['stat']) && $delivery['stat'] == false){
          Log::info("Order id - {$order->id} Delivery not Created");
        }else{
         
          OrderShipment::where('order_id',$order->id)->update([
            //'batch_id'  =>  $delivery['batch_id'],
            'amount'      =>  $delivery['fee']/100,
            'delivery_id' =>  $delivery['id'],
            'delivery'    =>  json_encode($delivery),
            'status'      =>  $delivery['status'],
            'shipment_id' =>  $delivery['id'],
            'tracking_url'=>  $delivery['tracking_url']
    
          ]);
        
        
        
        }
      }elseif($courier->name == 'USPS'){
        
        $delivery  = USPSHelper::CreateLabel($order->id,$orderitem->product_id);
        $seller = User::find($order->seller_id); 
        

        if($delivery['status'] == true){
        OrderShipment::where('order_id',$order->id)->update([
          //'batch_id'  =>  $delivery['batch_id'],
          //'amount'    =>  $delivery['fee']/100,
          'delivery_id' =>  $delivery['shipmentId'],
          //'delivery'  =>  json_encode($delivery),
          'status'      =>  'pending',
          'shipment_id' =>  $delivery['shipmentId'],
          'lable'       =>  $delivery['labelPath']
        //'tracking_url'=>  $delivery['tracking_url']
        ]);
        

        Helper::SendOrderLableMail($seller,$order->id);     
         }


      } 
        
     $user = User::find($order->user_id);
     
      $notification = [
     
        'title'    => "Payment Successful",
        'body'     => "Payment Successful",
        'message'  => "Hello $user->first_name, your payment has been successfully processed for order number $order->order_number",
        'sender_id'=> $order->orderitem->seller_id,
        'image_url'=> $order->orderitem?->product?->product_image ?? null, 
        'type'     => "Order_payment",
        'user_id'  => $user->id, 
        'order_id' => $order->id,

      ];

      Helper::OrderStatusCreate($order,'processing');
      
      try {
        NotificationHelper::sendNotifications($user->device_token,$notification,$data = []);
      } catch (\Throwable $th) {
        //throw $th;
      }
      
      $user->notify(new OrderNotification($notification));
      return Helper::ApiResponse(200, 'Payment succeeded, order details retrieved successfully', $response);

    }

      return Helper::ApiResponse(400, 'Event type not handled', []);     

    }

/**
 * Get User Transaction History
 *
 * Retrieves the transaction history for the authenticated user.
 *
 * @header Authorization Bearer token The access token of the authenticated user.
 * 
 * @response 200 {
 *   "status": 200,
 *   "message": "User Transaction History retrieved successfully",
 *   "data": [
 *     {
 *       "id": 1,
 *       "order_id": 12345,
 *       "amount": 100.50,
 *       "status": "completed",
 *       "payment_method": "card",
 *       "created_at": "2023-08-15 14:25:00",
 *       "updated_at": "2023-08-15 14:25:00",
 *       "order": {
 *         "id": 12345,
 *         "user_id": 1,
 *         "status": "delivered",
 *         "total": 150.75
 *       }
 *     },
 *     {
 *       "id": 2,
 *       "order_id": 67890,
 *       "amount": 250.00,
 *       "status": "pending",
 *       "payment_method": "paypal",
 *       "created_at": "2023-08-20 10:15:00",
 *       "updated_at": "2023-08-20 10:15:00",
 *       "order": {
 *         "id": 67890,
 *         "user_id": 1,
 *         "status": "processing",
 *         "total": 275.00
 *       }
 *     }
 *   ]
 * }
 */
public function transctionHistory()
{
    $orderIds = Order::where('seller_id', auth()->user()->id)->where('payment_status', 'paid')->pluck('id');
    
    $orderPayments = OrderPayment::with([
    'order' => function ($query) {

        $query->select('id','user_id','seller_id','order_number'); 
    
    },
    'order.orderItem' => function ($query) {
        $query->select('id', 'order_id', 'product_id'); 
    },
    'order.orderItem.product' => function ($query) {
        $query->select('id','product_name','size_id','user_id','price','image','created_at');
    }
    ])
    ->whereIn('order_id', $orderIds)
    ->get();

    return Helper::ApiResponse(200, 'User Transaction History retrieved successfully', $orderPayments);
}


public function orderPaymentCanceledWebhook(Request $request) {
  
  $data = $request->all();

  if ($data['type'] == 'payment_intent.succeeded') {
      $paymentIntent = $data['data']['object'];
      $response = [
          'payment_intent_id' => $paymentIntent['id'],
          'amount'   => $paymentIntent['amount'],
          'currency' => $paymentIntent['currency'],
          'status'   => $paymentIntent['status'],
      ];

      $payment = OrderPayment::where('stripe_intent_id',$paymentIntent['id'])->first();
      $order   = Order::where('id',$payment->order_id)->update([
        'payment_status' => 'unpaid',
        'delivery_status'=> 'pending'

      ]);
    
      $payment->update([
        'status' => 'Unpaid',
      ]);
        
      Helper::OrderStatusCreate($payment,'processing');
      return Helper::ApiResponse(200, 'Payment Canceled, Please Contact Admin', $response);
  }

}

public function OrderMarkasDeliverd($order_id,$type) {
  
     $order = Order::where('id',$order_id)->first();  
     $user = User::find($order->user_id);
     $seller = User::find($order->seller_id);
     if(!empty($order)){
       
       if($type == 'customer'){
        
        $order->update([
          'mark_as_delivered_customer' => 1,
   
        ]);
    
         if($seller->Stripe_connect_ac_id != null){
          $totalAmount = $order->net_amount - $order->seller_tax_amount;
          StripeHelper::payConnectedAccount(
            $seller->Stripe_connect_ac_id,
              $totalAmount,
              $user->first_name
          );
         }


      }elseif($type == 'seller'){

        $order->update([

          'mark_as_delivered__seller' => 1,
          'delivery_status' => 'delivered'
        ]);

        Helper::OrderStatusCreate($order,'delivered');

        $notification['title']   = "Order Delivered Successfully";
        $notification['body']    = "Order Delivered Successfully";
        $notification['message'] = "Hello $user->first_name, your order number $order->order_number has been delivered. Thank you for your purchase!";

        
        try {
            NotificationHelper::sendNotifications($user->device_token, $notification, []);
        } catch (\Throwable $th) {
            \Log::error("Notification error: " . $th->getMessage());
        }

        $user->notify(new OrderNotification($notification));
        Helper::SendOrderDeliveredMail($order->id);
     }
     }
    
     return Helper::ApiResponse(200, 'Order Deliverd Successfully', null);


}




}
