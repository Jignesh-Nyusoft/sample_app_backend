<?php

namespace App\Helpers;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Http;
use Log;
use Request;


class UberConnectHelper
{
    private static $token;
 
    
   public static function calculateDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $earthRadius = 3958.8; // Radius of the Earth in miles
    
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
    
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
    
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $earthRadius * $angle;
    }

    /**
     * Get Uber access token.
     */
    public static function authenticate()
    {
        
        if (!self::$token) {
       $response = Http::asForm()->post('https://login.uber.com/oauth/v2/token', [
                'client_id'     => Helper::_get_settings('UBER_CLIENT_ID'),
                'client_secret' => Helper::_get_settings('UBER_CLIENT_SECRET'),
                'grant_type'    => 'client_credentials',
                'scope'         => 'eats.deliveries',
            ]);

            $data = $response->json();
            self::$token = $data['access_token'];
        }
        

        return self::$token;
    }


    /**
     * Create a delivery request.
     */

   public static function createDelivery($product_id, $dropoff_id, $user_id,$order_id)
    {
        $shipment = OrderShipment::where('order_id',$order_id)->first();
         
        $token = self::authenticate();
        $url   = "https://api.uber.com/v1/customers/f99faccd-10be-454f-91a7-f94acba18a90/deliveries";
        #$url  = "https://api.uber.com/v1/customers/4b0cf4d8-f0c8-58ea-9b70-2fe13750ada1/deliveries";
        
        $dropoff     = Address::find($dropoff_id);
        $product     = Product::find($product_id);
        $seller_id   = $product->user_id;
        $pickup      = Address::where('user_id', $seller_id)->where('is_default_pickup', 1)->first();
        $seller      = User::find($seller_id);
        $user        = User::find($user_id);
    
        $pickupReadyDt     = Carbon::now()->addMinutes(30)->toISOString();  
        $pickupDeadlineDt  = Carbon::now()->addMinutes(50)->toISOString();  
        $dropoffReadyDt    = Carbon::now()->addMinutes(50)->toISOString();  
        $dropoffDeadlineDt = Carbon::now()->addMinutes(70)->toISOString();  
        
        $payload = [
               "order_id"            => $order_id,
                "pickup_name"        => $seller->first_name . ' ' . $seller->last_name ?? '',
                "pickup_address"     => json_encode([
                    'street_address' => ["$pickup->street"],
                    'city'           => $pickup->city,
                    'state'          => $pickup->state,
                    'zip_code'       => $pickup->zip_code,
                    'country'        => 'US'
                ]),

                "pickup_phone_number" => $seller->mobile,
                 "dropoff_name"        => $user->first_name . ' ' . $user->last_name ?? '',
                "dropoff_address"     => json_encode([
                    'street_address'  => ["$dropoff->street"],
                    'city'            => $dropoff->city,
                    'state'           => $dropoff->state,
                    'zip_code'        => $dropoff->zip_code,
                    'country'         => 'US'    
                ]),
                
                "dropoff_phone_number" => $user->mobile,
                "manifest_items" => [
                    [
                        "name"     => $product->product_name,
                        "quantity" => 1,
                        "price"    => intval($product->price),
                        "weight"   => 300
                    ]
                ],

                "pickup_latitude"    =>  floatval( number_format($pickup->location_lat, 25, '.', '')),
                "pickup_longitude"   =>  floatval( number_format($pickup->location_long, 25, '.', '')),
                "dropoff_latitude"   =>  floatval( number_format($dropoff->location_lat, 25, '.', '')),
                "dropoff_longitude"  =>  floatval( number_format($dropoff->location_long, 25, '.', '')),
                "pickup_ready_dt"    =>  $pickupReadyDt,
                "pickup_deadline_dt" =>  $pickupDeadlineDt,
                "dropoff_ready_dt"   =>  $dropoffReadyDt,
                "dropoff_deadline_dt"=>  $dropoffDeadlineDt,
                "tip"                =>  0,
                "idempotency_key"    =>  "1234567890",
                "external_store_id"  =>  "my_store_123",
                

            ];
        
    
        $response = Http::withToken($token)->withHeaders([

        'Content-Type' => 'application/json',
        
        ])->post($url, $payload);    
        
        if($response->status() == 200){

            return $response->json();

        }else{
            Log::info($response->json());
            $data = [
                'stat'     => false,
                'status'   => 500,
                'response' => $response->json(),
            ];
            return  $data;
    }
    }
    

    /**
     * Track the status of a delivery request.
     */
    public static function trackDelivery($deliveryId)
    {
        $token = self::authenticate();
        
        if (!$token) {
            Log::error('Authentication failed');
            return null; 
        }
    
        $response = Http::withToken($token)->get("https://api.uber.com/v1/deliveries/{$deliveryId}");
        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Uber API Error: ', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return null;
        }
    }


    /**
    * Cancel a delivery request.
    */
    public static function cancelDelivery($deliveryId)
    {
        $token = self::authenticate();
        $response = Http::withToken($token)->post("https://api.uber.com/v1/deliveries/{$deliveryId}/cancel");
        return $response->json();
    }

     /**
     * Estimate the delivery fee for a trip.
     */
    public static function createDeliveryQuote($product_id, $dropoff_id, $user_id)
    {
   
            $token    = self::authenticate();
            $dropoff  = Address::find($dropoff_id);
            $product  = Product::find($product_id);
            $seller_id   = $product->user_id;
            $pickup      = Address::where('user_id', $seller_id)->where('is_default_pickup', 1)->first();
            $seller      = User::find($seller_id);
            $user        = User::find($user_id);

            if(empty($pickup)){

                return false;
            }


            $distance = self::calculateDistance(
                $pickup->location_lat,
                $pickup->location_long,
                $dropoff->location_lat,
                $dropoff->location_long
            );
        
            if ($distance > 10) {
                return [
                    'stat' => false,
                    'error' => true,
                    'message' => "The dropoff location is outside the delivery radius of the pickup location. Distance: {$distance} miles, Max Radius: 10 miles."
                ];
            }


            $payload = [

                'pickup_address' => json_encode([
                    'street_address' => [$pickup->street],
                    'city'     => $pickup->city,
                    'state'    => $pickup->state,
                    'zip_code' => $pickup->zip_code,
                    'country'  => $pickup->country,
                ]),

                'dropoff_address' => json_encode([
                    'street_address' => [$dropoff->street],
                    'city'           => $dropoff->city,
                    'state'          => $dropoff->state,
                    'zip_code'       => $dropoff->zip_code,
                    'country'        => $dropoff->country,
                ]),

                'pickup_latitude'      => floatval( number_format($pickup->location_lat, 25, '.', '')),
                'pickup_longitude'     => floatval( number_format( $pickup->location_long, 25, '.', '')),
                'dropoff_latitude'     => floatval( number_format( $dropoff->location_lat, 25, '.', '')),
                'dropoff_longitude'    => floatval( number_format( $dropoff->location_long, 25, '.', '')),
                'pickup_phone_number'  => $seller->mobile,
                'dropoff_phone_number' => $user->mobile,
                'manifest_total_value' => 1000,
                'external_store_id'    => 'no_id',

            ];
           
            $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.uber.com/v1/customers/f99faccd-10be-454f-91a7-f94acba18a90/delivery_quotes", $payload);
            #->post("https://api.uber.com/v1/customers/4b0cf4d8-f0c8-58ea-9b70-2fe13750ada1/delivery_quotes", $payload);
        
            
            if ($response->successful()) {
                return $response->json(); 
            }else{
                
                return [
                    'stat'    => false,
                    'error'   => true,
                    'message' => $response->json()['message'] ?? 'An error occurred',
                    'status'  => $response->status(),
                ];

            }
        }
        
    /**
     * Handle Uber webhook events.
     */
    public static function handleWebhook(Request $request)
    {
        $event = $request->input('event_type');
        $deliveryId = $request->input('delivery_id');
        
        switch($event) {
        case 'delivery_status_changed':
                $newStatus = $request->input('status');
        break;

        }
    }   

     /**
     * Generate Uber Connect tracking URL for a delivery order.
     *
     * @param string $orderId The order ID returned from the Uber API.
     * @param string $tenantId The tenant ID used for the Uber environment.
     * @return string The generated tracking URL.
     */
    public static function generateTrackingUrl($orderId)
    {
               
        $baseUrl = 'https://delivery.uber.com/orders/';
        $queryString = '?ps=1&tenancyOverride=uber%2Ftesting%2Fdirect%2F';
       $tenantId = "f99faccd-10be-454f-91a7-f94acba18a90";
        #$tenantId = "4b0cf4d8-f0c8-58ea-9b70-2fe13750ada1";
        return $baseUrl . $orderId . $queryString . $tenantId;
    }
}

