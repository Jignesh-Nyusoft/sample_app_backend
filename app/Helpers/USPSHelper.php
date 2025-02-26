<?php

namespace App\Helpers;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\Product;
use App\Models\ProductCourierPartner;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Http;
use Request;

class USPSHelper
{

    public static function KeysAndData(){
         
        if(Helper::_get_settings('Environment') == "P"){
                
            
            return   $data = [ 
                'grant_type'    => 'client_credentials',
                'client_id'     => Helper::_get_settings('USPS_client_id_P'),  
                'client_secret' => Helper::_get_settings('USPS_client_secret_P'),  
                "CRID"  => Helper::_get_settings('USPS_CRID_P'), 
                "MID"   => Helper::_get_settings('USPS_MID_P'), 
                "manifestMID"   => Helper::_get_settings('USPS_ManifestMID_P'), 
                "accountType"   => Helper::_get_settings('USPS_AccountType_P'), 
                "accountNumber" => Helper::_get_settings('USPS_AccountNumber_P'), 
                "URL"           => "https://api.usps.com"  
             ];

        // USPS_client_id_P
        // USPS_client_secret_P
        // USPS_CRID_P
        // USPS_MID_P
        // USPS_ManifestMID_P
        // USPS_AccountType_P
        // USPS_AccountNumber_P
        
        }elseif(Helper::_get_settings('Environment') == "T"){
            return   $data = [
                'grant_type'    => 'client_credentials',
                'client_id'     => Helper::_get_settings('USPS_client_id_T'),  
                'client_secret' => Helper::_get_settings('USPS_client_secret_T'),  
                "CRID"  => Helper::_get_settings('USPS_CRID_T'), 
                "MID"   => Helper::_get_settings('USPS_MID_T'), 
                "manifestMID"   => Helper::_get_settings('USPS_ManifestMID_T'), 
                "accountType"   => Helper::_get_settings('USPS_AccountType_T'), 
                "accountNumber" => Helper::_get_settings('USPS_AccountNumber_T'), 
                "URL"           => "https://api-cat.usps.com" 
            
            ];

        }

    }





    public static function AuthoricationToken()
    { 
        $keys = self::KeysAndData();
        $Baseurl  = $keys['URL'];

        $data = [
            'grant_type' => 'client_credentials',
            'client_id'  => $keys['client_id'],  
            'client_secret' => $keys['client_secret'],  
        ];

        $response = Http::asForm()->post($Baseurl.'/oauth2/v3/token', $data);
        $responseData = $response->json();        
        return $responseData['access_token'] ?? null;

    }


    public static function paymentAuthorizationToken()
    {
        $keys = self::KeysAndData();
        $Baseurl  = $keys['URL'];

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . self::AuthoricationToken(), 
        ];

        $payload = [

            "roles" => [
                [
                    "roleName" => "LABEL_OWNER",
                    "CRID" => $keys['CRID'], 
                    "MID" => $keys['MID'], 
                    "manifestMID" => $keys['manifestMID'], 
                    "accountType" => $keys['accountType'], 
                    "accountNumber" => $keys['accountNumber'],
                ],
                [
                    "roleName" => "PAYER",
                    "CRID" => $keys['CRID'], 
                    "MID"  => $keys['MID'], 
                    "manifestMID"   => $keys['manifestMID'], 
                    "accountType"   => $keys['accountType'], 
                    "accountNumber" => $keys['accountNumber'],
                ]
            ]
        ];
        
    
        try {

            $response = Http::withHeaders($headers)->post($Baseurl.'/payments/v3/payment-authorization', $payload);
            
            if ($response->successful()) {
                $responsedata =$response->json(); 
                return $responsedata['paymentAuthorizationToken']; 
            }
    
            return [
                'error'   => true,
                'message' => $response->json('error.message') ?? 'An error occurred.',
                'details' => $response->json(),
            ];
        } catch (\Exception $e) {
            
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
    

   public static function getPackageDetails($category) {
       
        $baseDetails = [
            "weight" => 0,
            "length" => 0,
            "width"  => 0,
            "height" => 0,
            "mailClass" => "PRIORITY_MAIL",
            "processingCategory" => "MACHINABLE",
            "destinationEntryFacilityType" => "NONE",
            "extraServices" => null,
            "mailingDate" => Carbon::now()->addDay(1)->format('Y-m-d'),
            "rateIndicator" => "SP",
        ];
    
        if ($category == 'Small') {
            $baseDetails["processingCategory"] = "MACHINABLE"; 
        } elseif ($category == 'Medium') {
            $baseDetails["processingCategory"] = "MACHINABLE"; 
        } else {
            
            $baseDetails["processingCategory"] = "NON_MACHINABLE"; 
        }
    
        // Category-specific dimensions, weight, and mailing class
        switch ($category) {
            // case 'Small':
            //     $baseDetails["height"] = 6;  
            //     $baseDetails["length"] = 6; 
            //     $baseDetails["width"]  = 1;  
            //     $baseDetails["weight"] = 1; 
            //     $baseDetails["mailClass"] = "PRIORITY_MAIL"; 
            //     break;
    
            // case 'Medium':

            //     $baseDetails["height"] = 10;  
            //     $baseDetails["length"] = 24; 
            //     $baseDetails["width"]  = 14;  
            //     $baseDetails["weight"] = 2; 
            //     $baseDetails["mailClass"] = "PRIORITY_MAIL"; 
            //     break;
    
            // case 'Large':
               
            //     $baseDetails["height"] = 24;
            //     $baseDetails["length"] = 18;
            //     $baseDetails["width"] = 12;
            //     $baseDetails["weight"] = 10; 
            //     $baseDetails["mailClass"] = "PRIORITY_MAIL_EXPRESS"; 
            //     break;

            case 'Small':
                $baseDetails["height"] = 5;  
                $baseDetails["length"] = 9; 
                $baseDetails["width"]  = 7;  
                $baseDetails["weight"] = 1; 
                $baseDetails["mailClass"] = "PRIORITY_MAIL"; 
                break;

           case 'Medium':
                $baseDetails["height"] = 8;  
                $baseDetails["length"] = 12; 
                $baseDetails["width"]  = 10;  
                $baseDetails["weight"] = 2; 
                $baseDetails["mailClass"] = "PRIORITY_MAIL"; 
                break;

             case 'Large':
               
                $baseDetails["height"] = 10;
                $baseDetails["length"] = 16;
                $baseDetails["width"] = 12;
                $baseDetails["weight"] = 4; 
                $baseDetails["mailClass"] = "PRIORITY_MAIL"; 
                break;
    
            default:
                throw new Exception("Invalid package category provided");
        }
    
        return $baseDetails;
    }

    

    public static function CreateLabel($order_id,$product_id)
    {
        $keys = self::KeysAndData();
        $Baseurl  = $keys['URL'];
        $order       = Order::find($order_id);
        $user        = User::find($order->user_id);
        $product     = Product::find($product_id);
        $coruerSize  = ProductCourierPartner::where(['courier_name' => 'USPS','product_id' => $product->id])->first();
        $seller      = User::find($order->seller_id);
        $FromAddress = Address::where('user_id', $seller->id)->where('is_default_pickup', 1)->first();
        $Toaddress   = Address::find($order->address_id);
        $coruerSizeData = self::getPackageDetails($coruerSize->size);
        
        // $payload = [
        //     "toAddress" => [
        //         "firstName" => "Nick",
        //         "lastName" => "Fury",
        //         "streetAddress" => "2700 S Jefferson Ave",
        //         "secondaryAddress" => "STE 150", 
        //         "city" => "St. Louis",
        //         "state" => "MO",
        //         "ZIPCode" => "63104",
        //         "ZIPPlus4" => "2351"
        //     ],
        //     "fromAddress" => [
        //         "firstName" => "Sam",
        //         "lastName" => "Wilson",
        //         "streetAddress" => "311 Crossman St", 
        //         "secondaryAddress" => "Apt 200",
        //         "city" => "Jamestown",
        //         "state" => "NY",
        //         "ZIPCode" => "14701",
        //         "country" => "USA"
        //     ],
        //     "packageDescription" => [
        //         "weight" => 0.5,
        //         "length" => 5.0,
        //         "width" => 5.0,
        //         "height" => 4.0,
        //         "mailClass" => "PRIORITY_MAIL",
        //         "processingCategory" => "MACHINABLE",
        //         "destinationEntryFacilityType" => "NONE",
        //         "extraServices" => [920],
        //         "mailingDate" => "2025-01-16",
        //         "rateIndicator" => "SP",
        //     ],
        //     "imageInfo" => [
        //         "imageType" => "PDF",
        //         "labelType" => "4X6LABEL",
        //         "receiptOption" => "SAME_PAGE",
        //         "suppressPostage" => true,
        //         "suppressMailDate" => true,
        //         "returnLabel" => false
        //     ]
        // ];

        $payload = [
            "toAddress" => [
                "firstName" => $user->first_name,
                "lastName" => $user->last_name,
                "streetAddress" => $Toaddress->street,
                "secondaryAddress" => $Toaddress->house_no, 
                "city" => $Toaddress->city,
                "state" => Helper::getStateShortName($Toaddress->state),
                "ZIPCode" => $Toaddress->zip_code,
                // "ZIPPlus4" => "2351"
            ],
            "fromAddress"   => [
                "firstName" => $seller->first_name,
                "lastName"  => $seller->last_name,
                "streetAddress"    => $FromAddress->street, 
                "secondaryAddress" => $FromAddress->house_no,
                "city"    => $FromAddress->city,
                "state"   => Helper::getStateShortName($FromAddress->state),
                "ZIPCode" => $FromAddress->zip_code,
                "country" => "USA"
            ],

            //     "toAddress" => [
            //     "firstName" => "Nick",
            //     "lastName" => "Fury",
            //     "streetAddress" => "2700 S Jefferson Ave",
            //     "secondaryAddress" => "STE 150", 
            //     "city" => "St. Louis",
            //     "state" => Helper::getStateShortName($Toaddress->state),
            //     "ZIPCode" => "63104",
            //     "ZIPPlus4" => "2351"
            // ],
            // "fromAddress" => [
            //     "firstName" => "Sam",
            //     "lastName" => "Wilson",
            //     "streetAddress" => "311 Crossman St", 
            //     "secondaryAddress" => "Apt 200",
            //     "city" => "Jamestown",
            //     "state" => Helper::getStateShortName($FromAddress->state),
            //     "ZIPCode" => "14701",
            //     "country" => "USA"
            // ],

            "packageDescription" => [
                "weight" => $coruerSizeData['weight'],
                "length" => $coruerSizeData['length'],
                "width" =>  $coruerSizeData['width'],
                "height" => $coruerSizeData['height'],
                "mailClass" => $coruerSizeData['mailClass'],
                "processingCategory" => $coruerSizeData['processingCategory'],
                "destinationEntryFacilityType" => "NONE",
                // "extraServices" => [920],
                "mailingDate"   => $coruerSizeData['mailingDate'],
                "rateIndicator" => $coruerSizeData['rateIndicator'],
            ],

            "imageInfo" => [
                "imageType" => "PDF",
                "labelType" => "4X6LABEL",
                "receiptOption" => "SAME_PAGE",
                "suppressPostage" => true,
                "suppressMailDate" => true,
                "returnLabel" => false
            ]

        ];
        

        $headers = [
            'Authorization' => 'Bearer ' . self::AuthoricationToken(),
            'X-Payment-Authorization-Token' => self::paymentAuthorizationToken(),
            'Accept' => 'application/vnd.usps.labels+json', 
            'Content-Type' => 'application/json', 
        ];
        
        
         try {
            $response  = Http::withHeaders($headers)->post($Baseurl.'/labels/v3/label', $payload);
            $label =  $response->json();
            if (isset($label['labelImage'])) {
                $labelDirectory = public_path('Lable');
                if (!file_exists($labelDirectory)) {
                    mkdir($labelDirectory, 0755, true); // Create the directory if it doesn't exist
                }
                
                $base64String = $label['labelImage'];
                $pdfData = base64_decode($base64String);
                
                
                $labelDirectory = public_path('Lable');
                $filePath = $labelDirectory . '/label_' . $label['trackingNumber'] . '.pdf';
                file_put_contents($filePath, $pdfData);
                
                self::CreateSubscribe( $user->email,$label['trackingNumber'],$order);

                $data = [
                    'status'     => true,
                    'shipmentId' => $label['trackingNumber'],
                    'labelPath'  => 'Lable/label_' . $label['trackingNumber'] . '.pdf',

                ];
                return $data;
            }
         } catch (\Throwable $th) {
            

            $data = ['status' => false];
            return $data;
        }
        // if($response->successful()) {
        //     $label =  $response->json();
        //     return response()->json([
        //         'shipmentId' => $label['trackingNumber'], 
        //         'labelImage' => isset($label['labelImage']) ? 'data:image/pdf;base64,' . $label['labelImage'] : null, // For embedded Base64 image
        //     ]);
        // }
    
    }
    

    /**
     * Create a delivery quote based on the product, dropoff address, and user information.
     *
     * @param  int|string  $product_id
     * @param  int|string  $dropoff_id
     * @param  int|string  $user_id
     */
     public static function createDeliveryQuote($product_id,$dropoff_id,$user_id) {

        $keys = self::KeysAndData();
        $Baseurl = $keys['URL'];
        $product = Product::find($product_id);
        $coruerSize  = ProductCourierPartner::where(['courier_name' => 'USPS','product_id' => $product->id])->first();  
        $dropoffAddress = Address::find($dropoff_id);
        $user    = User::find($user_id);
        $selleraddress  = Address::where('is_default_pickup',1)->where('user_id',$product->user_id)->first();
       
        if (!$product || !$dropoffAddress || !$user) {
            return ['error' => 'Missing necessary information for delivery quote.'];
        }

        $coruerSizeData = self::getPackageDetails($coruerSize->size);
         
        $origin_zip      = $selleraddress->zip_code;
        $destination_zip = $dropoffAddress->zip_code;
      
        $packageDetails = [
            "destinationEntryFacilityType" => "NONE", 
            "destinationZIPCode" => $destination_zip,          
            "height" => $coruerSizeData['height'],                           
            "length" => $coruerSizeData['length'],              
            "weight" => $coruerSizeData['weight'],                           
            "width"  => $coruerSizeData['width'],                                          
            "mailClass" => $coruerSizeData['mailClass'],    
            "originZIPCode" => $origin_zip,               
            "priceType" => "COMMERCIAL",                  
            "processingCategory" => $coruerSizeData['processingCategory'],     
            "rateIndicator" => "SP",                  
            "mailingDate" => $coruerSizeData['mailingDate'],            
        ];


        //$packageDetails = [
        //     "destinationEntryFacilityType" => "NONE", 
        //     "destinationZIPCode" => $destination_zip,          
        //     "height" => 10,                           
        //     "length" => 1,                            
        //     "mailClass" => "USPS_GROUND_ADVANTAGE",    
        //     "originZIPCode" => $origin_zip,               
        //     "priceType" => "RETAIL",                  
        //     "processingCategory" => "MACHINABLE",     
        //     "rateIndicator" => "SP",                  
        //     "weight" => 1,                           
        //     "width" => 15,                            
        //     "mailingDate" => "2025-01-09",            
        // ];


        $headers = [
            'Authorization' =>'Bearer '.self::AuthoricationToken(),  
            'Content-Type' => 'application/json',
        ];  
        
        try {
            $response = Http::withHeaders($headers)->post($Baseurl.'/prices/v3/base-rates/search', $packageDetails);


            if ($response->successful()) {
                
                return $response->json(); 
            
            } else {
            
                return false;
            
            }
        } catch (\Throwable $th) {
            return false;
        }

    
    }
    


    /**
     * Get tracking information for a shipment.
     *
     * @param  string  $tracking_number
     * @return array
     */
    public static function getTrackingInformation($tracking_number) {
        $url = "https://api-cat.usps.com/v3/track";
    
        $payload = [
            "TrackRequest" => [
                "USERID" => Helper::_get_settings('USPS_CLIENT_ID'),
                "TrackID" => [
                    "ID" => $tracking_number
                ]
            ]
        ];
    
        $response = Http::post($url, $payload);
    
        if ($response->failed()) {
            return ['error' => 'Failed to fetch tracking information.'];
        }
    
        $trackingInfo = [];
        foreach ($response->json('TrackResponse.TrackInfo') as $info) {
            $trackingInfo[] = [
                'status' => $info['TrackSummary'] ?? 'N/A',
                'date' => $info['TrackDetail']['EventDate'] ?? 'N/A',
                'time' => $info['TrackDetail']['EventTime'] ?? 'N/A',
            ];
        }
    
        return $trackingInfo;
    }
    
    

    /**
     * Create a shipment using USPS API.
     *
     * @param  int  $order_id
     * @param  int  $shipping_method
     * @return array
     */
    public static function createShipment($order_id, $shipping_method) {
        $order = Order::find($order_id);
        $shippingAddress = $order->shippingAddress;
    
        if (!$order || !$shippingAddress) {
            return ['error' => 'Missing necessary order or shipping information.'];
        }
    
        $payload = [
            "ShipmentRequest" => [
                "USERID" => Helper::_get_settings('USPS_CLIENT_ID'),
                "Package" => [
                    [
                        "Service" => $shipping_method,
                        "ZipOrigination" => $order->user->address->zip_code,
                        "ZipDestination" => $shippingAddress->zip_code,
                        "Weight" => [
                            "Pounds" => $order->total_weight,
                            "Ounces" => ($order->total_weight * 16)
                        ],
                        "Address" => [
                            "Address1" => $shippingAddress->address_line1,
                            "City" => $shippingAddress->city,
                            "State" => $shippingAddress->state,
                            "PostalCode" => $shippingAddress->zip_code,
                            "Country" => $shippingAddress->country,
                        ]
                    ]
                ]
            ]
        ];
    
        $url = "https://api-cat.usps.com/v3/shipment";
        $response = Http::post($url, $payload);
    
        if ($response->failed()) {
            return ['error' => 'Failed to create shipment.'];
        }
    
        return [
            'shipment_id' => $response->json('ShipmentResponse.Package.TrackingNumber'),
            'tracking_url' => 'https://tools.usps.com/find-location.htm?loc=1&track=' . $response->json('ShipmentResponse.Package.TrackingNumber'),
        ];
    }
    
    
    /**
     * Get shipping services available from USPS.
     *
     * @return array
     */
    public static function getAvailableShippingServices() {
        return [
            'Priority Mail',
            'Priority Mail Express',
            'First-Class Mail',
            'Media Mail',
            'Parcel Select',
        ];
    }

    /**
     * Get package weight in ounces.
     *
     * @param  int  $product_id
     * @return int
     */
    public static function getPackageWeightInOunces($product_id) {
        $product = Product::find($product_id);
        if (!$product) {
            return 0;
        }


        return $product->weight * 16; 
    }



    public function handleUSPSWebhook(Request $request)
{
    $data = $request->all();

    $secret = config('services.usps.secret');
    $receivedHash = $request->header('X-USPS-Hmac-Signature');
    $calculatedHash = hash_hmac('sha256', $request->getContent(), $secret);

    if ($receivedHash !== $calculatedHash) {
        return response()->json(['error' => 'Invalid HMAC signature'], 401);
    }

    
    $trackingNumber = $data['trackingNumber'];
    $status = $data['status'];

    return response()->json(['message' => 'Webhook received successfully']);
}



public static function getSubscription($subscriptionId)
{
    $endpoint = "https://api.usps.com/subscriptions-tracking/v3/subscriptions/subscriptions/{$subscriptionId}";
    $apiKey = config('services.usps.api_key'); 

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
    ])->get($endpoint);

    return $response->json();
}



public static function CreateSubscribe($selleremail,$shipmentid) {

    $keys = self::KeysAndData();
    $Baseurl  = $keys['URL'];
    
    $secret = $keys['client_secret'];  
    $accessToken =  self::AuthoricationToken();  

    $shipment_id = $shipmentid;
    #$shipment_id = $shipmentid;
      

    $eventData = json_encode([
        "trackingNumber" => $shipment_id,
    ]);

    $hash = hash('sha256', $eventData . $secret);
     
    $secret = substr($hash, 0, 32);
    $payload = [
        "listenerURL" => "https://greenclusta.com/api/handel-USPS-status",
        "secret"      => $secret,  
        "adminNotification" => [
            [
                "email" => $selleremail,
            ]
        ],
        "filterProperties" => [

            "trackingNumber"     => $shipment_id,
            "trackingEventTypes" => ["ALL"],  
        
        ]
    ];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,  
    ])->post($Baseurl.'/subscriptions-tracking/v3/subscriptions', $payload);
 
    
 return true;  

}








}
