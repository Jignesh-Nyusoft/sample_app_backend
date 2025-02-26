<?php 

namespace App\Helpers;
use App\Mail\OtpMail;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Str;
use GuzzleHttp\Client;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationHelper 
{

    
    public static function sendNotifications($deviceToken, $notification, $data = [])
    {
       
        if (empty($deviceToken)) {
            return false; 
        }
    
        $firebase = (new Factory)
        ->withServiceAccount(storage_path('firebase-service-account.json'));
    
        $messaging = $firebase->createMessaging();
    
        $notification = [
            'title'     => $notification['title'] ?? '',
            'body'      => $notification['body'] ?? '',
            'message'   => $notification['message'] ?? '',
            'sound'     => 'default',
            'sender_id' => $notification['sender_id'] ?? '',
            'image_url' => $notification['image_url'] ?? '',
            'type'      => $notification['type'] ?? '',
            'user_id'   => $notification['user_id'] ?? '',
            'order_id'  => $notification['order_id'] ?? '',
        ];
    
        try {
            $payload = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification([
                    'title' => $notification['title'],
                    'body'  => $notification['body'],
                ])
                ->withData($data);
    
            $response = $messaging->send($payload);
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }
    

    
  public static function  sendNotification($deviceToken=null, $title, $body,$message,$data = [])
  {
        $client = new Client();
            
        $serverKey = "AIzaSyBeeE4HIxeVZ0WMJt_qsgwcusWYhGT7U4g";

        $url = 'https://fcm.googleapis.com/fcm/send';

        $notification = [

            'title'   => $title,
            'body'    => $body,
            'message' => $message,
            'sound'   => 'default',
        
        ];
    
        $payload = [
            'to'           => $deviceToken,
            'notification' => $notification,
            'data'         => $data,
        ];

        $response = $client->post($url,[
            'headers' => [
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => $payload,
        ]);

        try {

    
            return $response->getStatusCode() == 200;
        } catch (\Exception $e) {
    
            return false;
        }
    }
    



}