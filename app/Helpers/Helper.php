<?php 

namespace App\Helpers;
use App\Mail\OtpMail;
use App\Models\Coupon;
use App\Models\CourierPartner;
use App\Models\EmailTemplateModel;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderShipment;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Str;

class Helper 
{ 

public static function _get_settings($field)
{
    $result =  Setting::where('field',$field)->first();

    if($result){
        return $result->value;
    }else{
        return null;
    }

}
    
public static function get_settings( $field , $decrypt='false') {
    if( isset($field) && $field  && !empty($field)):
        if( $decrypt == 'true')
        {
           $data = Setting::where('field',$field)->first();

           if(isset($data['value']) && !empty($data['value']) &&  $decrypt == 'true')
           {
            try{
                $value = Crypt::decryptString(base64_decode($data['value'] ));
                return $value;
            }catch (\Throwable $th) {
              return $data['value'];
            }
            }
            }
            else
            {
                return Setting::where('field',trim($field))->value('value');
            }
    endif;
    return false;
}


/**
 * Converts a string into a URL-friendly slug.
 *
 * @param string $string The input string to be converted
 * @return string The resulting slug, with lowercase letters and hyphens
 */
public static function createSlug($string)
{
       $slug = strtolower($string);
       $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
       $slug = preg_replace('/-+/', '-', $slug);
       $slug = trim($slug, '-');
       
       return $slug;
}


/**
 * Uploads an image file to a specified directory and returns its path.
 *
 * @param \Illuminate\Http\UploadedFile $image The image file to upload
 * @param string $path The directory path where the image will be stored
 * @param string|null $old Optional. The old file name to be replaced (if any)
 * @return string The path of the uploaded image file
 */
public static function UploadImage($image,$path,$old = null){
    
    if ($old && file_exists(public_path($old))) {

        unlink(public_path($old));
    }
    
    $file = $image;
    $extension  = $file->getClientOriginalExtension();
    $safeName   = Str::random(10) . '.' . $extension;
    $publicPath = public_path($path);
    $file->move($publicPath, $safeName);
    $filePath   = $path . '/' . $safeName;

    return $filePath;

}

     
public static function Dashboard($key){

    if($key == 'user_count'){

     return User::where('role','!=','Admin')->count(); 
    
    }elseif($key == 'total_orders'){

     return Order::count();

    }elseif($key == 'total_orders'){
        
     return Product::count();   
    
    }elseif($key == 'product_count'){
     
     return Product::count();

    }elseif($key == 'seller_sale'){
     
     return Order::sum('total_amount') ?? 0;    
     
    }elseif ($key == 'recent_order') {
        return Order::where('created_at', '>=', now()->subDay())->count();
    }else{
        return null;
    }
}

/**
 * Generate a JSON response for API.
 *
 * @param int $status HTTP status code
 * @param string $message Response message
 * @param mixed $data Additional data (optional)
 * @return \Illuminate\Http\JsonResponse
 */
 public static function ApiResponse($status,$message,$data = null) { 
 
    $data = [
        'status' => $status,
        'message'=> $message,
        'data'   => $data,
    ];

 return response()->json($data,$status);

 }


 public static function SendOtp($user,$otp) {

 }

 public static function SendOtpViaMail($user,$otp) {

    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,800&display=swap" rel="stylesheet">
    <style media="screen" type="text/css">
       body {
        font-family: "Poppins", sans-serif;
       }
    </style>

    <table cellpadding="0" cellspacing="0" style="width:600px;margin:auto;font-family:Arial,Helvetica,sans-serif;background:#edf1f7;font-family: \'Poppins\', sans-serif;">
        <thead>
            <tr>
                <th style="background: #2A3F54; padding: 50px;" colspan="4">
                    <a href="{BASE_URL}">
                        <img style ="width:250px;" src="{LOGO}" alt="Logo">
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
                <td style="background:#ffffff;padding:20px;text-align: center; font-size: 20px; line-height: 20px; font-weight: 700;">
                    OTP Verification
                </td>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 14px; line-height: 16px;">
                    Dear <b>{NAME}</b>, You are receiving this email because you recently requested OTP verification for your {SITENAME} account.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 14px; line-height: 16px;">
                    Your OTP is:
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 20px; font-weight: 700; color: #2A3F54;">
                    {OTP}
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 14px; line-height: 16px;">
                    If you did not request this OTP, please contact <a href="{LINK_1}" style="color: #2A3F54; text-decoration: none;">support</a> for assistance.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center;">Thank You, {SITENAME} Support Team</td>
            </tr>
        </tfoot>
    </table>
    </html>
';

$htmlContent = str_replace(
    ['{NAME}', '{OTP}', '{BASE_URL}', '{LOGO}', '{SITENAME}', '{LINK_1}'],
    [$user->first_name, $otp, 'https://2ndfusion.nyusoft.in/', asset(self::get_settings('logo')), self::get_settings('site_name'), 'https://2ndfusion.nyusoft.in/'],
    $htmlContent
);




 try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = self::_get_settings('smtp_host'); 
    $mail->SMTPAuth   = true;
    $mail->Username   = self::_get_settings('smtp_username');
    $mail->Password   = "ADEK2027@@@";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587; 
    
    
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];
    
    $mail->setFrom(self::_get_settings('smtp_email'), 'GreenClusta');
    $mail->addAddress($user->email);
    
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body    = $htmlContent;
    $mail->AltBody = "Your OTP is: {$otp}";
    
    $mail->send();

   return  true;
 } catch (\Throwable $th) {
    return false;
 }
 
}

public static function SendChangePasswordViaMail($user) {


    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,800&display=swap" rel="stylesheet">
    <style media="screen" type="text/css">
       body {
        font-family: "Poppins", sans-serif;
       }
    </style>

    <table cellpadding="0" cellspacing="0" style="width:600px;margin:auto;font-family:Arial,Helvetica,sans-serif;background:#edf1f7;font-family: \'Poppins\', sans-serif;">
        <thead>
            <tr>
                <th style="background: #2A3F54; padding: 50px;" colspan="4">
                    <a href="{BASE_URL}">
                        <img style="width:250px;" src="{LOGO}" alt="Logo">
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
                <td style="background:#ffffff;padding:20px;text-align: center; font-size: 20px; line-height: 20px; font-weight: 700;">
                    Password Changed Successfully
                </td>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 14px; line-height: 16px;">
                    Dear <b>{NAME}</b>, Your password has been successfully changed for your {SITENAME} account.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 14px; line-height: 16px;">
                    If you did not make this change, please contact <a href="{LINK_1}" style="color: #2A3F54; text-decoration: none;">support</a> immediately to secure your account.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
        </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center;">Thank You, {SITENAME} Support Team</td>
            </tr>
        </tfoot>
    </table>
    </html>';


$htmlContent = str_replace(
    ['{NAME}','{BASE_URL}', '{LOGO}', '{SITENAME}', '{LINK_1}'],
    [$user->first_name,'https://2ndfusion.nyusoft.in/', asset(self::get_settings('logo')), self::get_settings('site_name'), 'https://2ndfusion.nyusoft.in/'],
    $htmlContent
);


 try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = self::_get_settings('smtp_host'); 
    $mail->SMTPAuth   = true;
    $mail->Username   = self::_get_settings('smtp_username');
    $mail->Password   = "ADEK2027@@@";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587; 
    
    
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];
    
    $mail->setFrom(self::_get_settings('smtp_email'), 'GreenClusta');
    $mail->addAddress($user->email);
    
    $mail->isHTML(true);
    $mail->Subject = 'Password Changed Successfully';
    $mail->Body    = $htmlContent;
    $mail->AltBody = "Password Changed Successfully";    
    $mail->send();

   return  true;
 } catch (\Throwable $th) {
    return false;
 }
 
}




public static function SendProductSoldOutMail($order) {

    $order     = Order::find($order);
    $user      = User::find($order->seller_id);
    $orderitem = OrderItems::where('order_id',$order->id)->first();
    $product   = Product::find($orderitem->product_id); 
    $orderShipment = OrderShipment::where('order_id',$order->id)->first();
    $courier = CourierPartner::find($orderShipment->courier_partner_id);
      
     if($courier->name == "USPS"){

        $message   = "You will receive USPS label in your mail. You can use it to ship your package by submitting it to a nearby USPS postal office";
    
    }elseif($courier->name == "Uber"){

        $message   = "Keep your package ready for delivery. An Uber delivery partner will pickup the package from your address";
     
    }else{

        $message   = "Press Mark As Delivered Button in your product details once you handover the product";
     } 



    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,800&display=swap" rel="stylesheet">
    <style media="screen" type="text/css">
       body {
        font-family: "Poppins", sans-serif;
       }
    </style>
    
    <table cellpadding="0" cellspacing="0" style="width:600px;margin:auto;font-family:Arial,Helvetica,sans-serif;background:#edf1f7;font-family: \'Poppins\', sans-serif;">
        <thead>
            <tr>
                <th style="background: #2A3F54; padding: 50px;" colspan="4">
                    <a href="{BASE_URL}">
                        <img style="width:250px;" src="{LOGO}" alt="Logo">
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
                <td style="background:#ffffff;padding:20px;text-align: center; font-size: 20px; line-height: 20px; font-weight: 700;">
                    Product Sold Out Notification
                </td>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:20px;text-align: left; font-size: 14px; line-height: 20px;">
                    Dear {CUSTOMER_NAME},
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>

            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;">
                    <table style="width:100%;border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Order Number:</td>
                            <td style="padding:8px;border:1px solid #eee;">{ORDER_NUMBER}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Product Name:</td>
                            <td style="padding:8px;border:1px solid #eee;">{PRODUCT_NAME}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:20px;text-align: left; font-size: 14px; line-height: 20px;">
                    {MSG}.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center;">Thank You, {SITENAME} Support Team</td>
            </tr>
        </tfoot>
    </table>
    </html>';
    

    $htmlContent = str_replace(
        ['{CUSTOMER_NAME}', '{ORDER_NUMBER}', '{PRODUCT_NAME}', '{BASE_URL}', '{LOGO}', '{SITENAME}','{MSG}'],
        [
            $user->first_name,
            $order->order_number,
            $product->product_name,
            'https://2ndfusion.nyusoft.in/',
            url(Helper::_get_settings('logo')),
            self::get_settings('site_name'),
            $message,
        ],
        $htmlContent
    );

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = self::_get_settings('smtp_host'); 
        $mail->SMTPAuth   = true;
        $mail->Username   = self::_get_settings('smtp_username');
        $mail->Password   = "ADEK2027@@@";
        // $mail->Host       = "smtp.gmail.com"; 
        // $mail->SMTPAuth   = true;
        // $mail->Username   = "aws5.nyusoft@gmail.com";
        // $mail->Password   = "uokfxkdjepbfbods";


         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
        $mail->Port       = 587; 
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        
        $mail->setFrom(self::_get_settings('smtp_email'), 'GreenClusta');
        #$mail->setFrom("juber.sheikh@nyusoft.com", 'greenclusta.com');
        $mail->addAddress($user->email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Product Sold Out & Shipping details';
        $mail->Body    = $htmlContent;
        $mail->AltBody = "Product Sold Out & Shipping details";
        
        $mail->send();
        
        return true;
    } catch (\Throwable $th) {
        return false;
    }
}

public static function SendOrderLableMail($user,$order_id){

    $orderlable = OrderShipment::where('order_id',$order_id)->first(); 
    $order = Order::find($order_id);
    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,800&display=swap" rel="stylesheet">
    <style media="screen" type="text/css">
       body {
        font-family: "Poppins", sans-serif;
       }
    </style>
    
    <table cellpadding="0" cellspacing="0" style="width:600px;margin:auto;font-family:Arial,Helvetica,sans-serif;background:#edf1f7;font-family: \'Poppins\', sans-serif;">
        <thead>
            <tr>
                <th style="background: #2A3F54; padding: 50px;" colspan="4">
                    <a href="{BASE_URL}">
                        <img style="width:250px;" src="{LOGO}" alt="Logo">
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
                <td style="background:#ffffff;padding:20px;text-align: center; font-size: 20px; line-height: 20px; font-weight: 700;">
                    Order Label Created Successfully
                </td>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 14px; line-height: 16px;">
                    Dear <b>{USER_NAME}</b>, <br>Your USPS shipping label for Order <b>#{ORDER_NUMBER}</b> has been successfully created. 
                    The label is attached to this email. You can use it to ship your package by submitting it to a nearby USPS postal office.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: center; font-size: 14px; line-height: 16px;">
                    If you have any questions or need further assistance, please contact <a href="" style="color: #2A3F54; text-decoration: none;">support</a>.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center;">Thank You, {SITENAME} Support Team</td>
            </tr>
        </tfoot>
    </table>
    </html>';
    
    $htmlContent = str_replace(
        ['{USER_NAME}', '{USER_EMAIL}', '{USER_PHONE}', '{BASE_URL}', '{LOGO}', '{SITENAME}','{ORDER_NUMBER}'],
        [
            $user->first_name, 
            $user->email, 
            $user->phone ?? 'N/A',  
            'https://2ndfusion.nyusoft.in/', 
            asset(self::get_settings('logo')), 
            self::get_settings('site_name'),
            $order->order_number,
        ],
        $htmlContent
    );
    
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = self::_get_settings('smtp_host'); 
    $mail->SMTPAuth   = true;
    $mail->Username   = self::_get_settings('smtp_username');
    $mail->Password   = "ADEK2027@@@";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587; 
    
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];
    
    $mail->setFrom(self::_get_settings('smtp_email'), 'GreenClusta');
    $mail->addAddress($user->email);
    
    $uspsLabelPath = $orderlable->lable; 
    if (file_exists($uspsLabelPath)) {
        $mail->addAttachment($uspsLabelPath, 'USPS_Label.pdf'); 
    } else {
        throw new Exception("USPS label file not found at: $uspsLabelPath");
    }
    
    $mail->isHTML(true);
    $mail->Subject = 'USPS Label Attached';
    $mail->Body    = $htmlContent;
    $mail->AltBody = "USPS label has been attached to this email.";
    
    $mail->send();
    echo "Mail sent successfully!";
} catch (Exception $e) {
    echo "Mail could not be sent. Error: {$mail->ErrorInfo}";
}
}


public static function SendOrderDeliveredMail($order) {
      
    $order = Order::find($order);
    $user  = User::find($order->user_id); 

    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,800&display=swap" rel="stylesheet">
    <style media="screen" type="text/css">
       body {
        font-family: "Poppins", sans-serif;
       }
    </style>

    <table cellpadding="0" cellspacing="0" style="width:600px;margin:auto;font-family:Arial,Helvetica,sans-serif;background:#edf1f7;font-family: \'Poppins\', sans-serif;">
        <thead>
            <tr>
                <th style="background: #2A3F54; padding: 50px;" colspan="4">
                    <a href="{BASE_URL}">
                        <img style="width:250px;" src="{LOGO}" alt="Logo">
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
                <td style="background:#ffffff;padding:20px;text-align: center; font-size: 20px; line-height: 20px; font-weight: 700;">
                    Order Delivered Successfully
                </td>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:20px;text-align: left; font-size: 14px; line-height: 20px;">
                    Dear {CUSTOMER_NAME},
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: left; font-size: 14px; line-height: 20px;">
                    We are pleased to inform you that your order has been successfully delivered. Below are the details of your order:
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;">
                    <table style="width:100%;border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Order ID:</td>
                            <td style="padding:8px;border:1px solid #eee;">{ORDER_ID}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Order Date:</td>
                            <td style="padding:8px;border:1px solid #eee;">{ORDER_DATE}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Total Amount:</td>
                            <td style="padding:8px;border:1px solid #eee;">{TOTAL_AMOUNT}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:20px;text-align: left; font-size: 14px; line-height: 20px;">
                    Thank you for shopping with us. We hope to serve you again soon!
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center;">Thank You, {SITENAME} Support Team</td>
            </tr>
        </tfoot>
    </table>
    </html>';

    $htmlContent = str_replace(
        ['{CUSTOMER_NAME}', '{ORDER_ID}', '{ORDER_DATE}','{TOTAL_AMOUNT}', '{BASE_URL}', '{LOGO}', '{SITENAME}'],
        [
            $user->first_name,
            $order->order_number,
            $order->created_at->format('d-m-Y'),
            number_format($order->total_amount, 2),
            'https://2ndfusion.nyusoft.in/',
            asset(self::get_settings('logo')),
            self::get_settings('site_name')
        ],
        $htmlContent
    );

    try {
    
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = self::_get_settings('smtp_host'); 
        $mail->SMTPAuth   = true;
        $mail->Username   = self::_get_settings('smtp_username');
        $mail->Password   = "ADEK2027@@@";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587; 
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        
        $mail->setFrom(self::_get_settings('smtp_email'), 'GreenClusta');
        $mail->addAddress($user->email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Order Delivered Successfully';
        $mail->Body    = $htmlContent;
        $mail->AltBody = "Order Delivered Successfully";

        
        $mail->send();     
    
       return  true;
     } catch (\Throwable $th){

        return false;
     }
}





public static function SendContactViaMail($contact) {


    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,800&display=swap" rel="stylesheet">
    <style media="screen" type="text/css">
       body {
        font-family: "Poppins", sans-serif;
       }
    </style>

    <table cellpadding="0" cellspacing="0" style="width:600px;margin:auto;font-family:Arial,Helvetica,sans-serif;background:#edf1f7;font-family: \'Poppins\', sans-serif;">
        <thead>
            <tr>
                <th style="background: #2A3F54; padding: 50px;" colspan="4">
                    <a href="{BASE_URL}">
                        <img style="width:250px;" src="{LOGO}" alt="Logo">
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
                <td style="background:#ffffff;padding:20px;text-align: center; font-size: 20px; line-height: 20px; font-weight: 700;">
                    New Contact Us Submission
                </td>
                <td style="width:10%;background:#2A3F54;padding:20px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:20px;text-align: left; font-size: 14px; line-height: 20px;">
                    Dear Admin,
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;text-align: left; font-size: 14px; line-height: 20px;">
                    You have received a new message through the contact form. Below are the details:
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:0 20px 15px;">
                    <table style="width:100%;border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Name:</td>
                            <td style="padding:8px;border:1px solid #eee;">{USER_NAME}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Email:</td>
                            <td style="padding:8px;border:1px solid #eee;">{USER_EMAIL}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Phone:</td>
                            <td style="padding:8px;border:1px solid #eee;">{USER_PHONE}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;font-weight:bold;">Message:</td>
                            <td style="padding:8px;border:1px solid #eee;">{USER_MESSAGE}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
            <tr>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
                <td style="background:#ffffff;padding:20px;text-align: left; font-size: 14px; line-height: 20px;">
                    Please review and respond to the user as necessary.
                </td>
                <td style="width:10%;background:#edf1f7;padding:0 20px 15px"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center;">Thank You, {SITENAME} Support Team</td>
            </tr>
    </tfoot>
    </table>
    </html>';



$htmlContent = str_replace(
    ['{USER_NAME}', '{USER_EMAIL}', '{USER_PHONE}', '{USER_MESSAGE}', '{BASE_URL}', '{LOGO}', '{SITENAME}'],
    [
        $contact->first_name, 
        $contact->email, 
        $contact->phone ?? 'N/A', 
        $contact->message, 
        'https://2ndfusion.nyusoft.in/', 
        asset(self::get_settings('logo')), 
        self::get_settings('site_name')
    ],
    $htmlContent
);



 try {
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = self::_get_settings('smtp_host'); 
    $mail->SMTPAuth   = true;
    $mail->Username   = self::_get_settings('smtp_username');
    $mail->Password   = "ADEK2027@@@";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587; 
    
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];
    
    $mail->setFrom(self::_get_settings('smtp_email'), 'GreenClusta');
    $mail->addAddress("support@greenclusta.com");
    
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Query';
    $mail->Body    = $htmlContent;
    $mail->AltBody = "New Contact Query";
    
    $mail->send();     

   return  true;
 } catch (\Throwable $th) {
    return false;
 }
 
}



public static function OrderStatusCreate($orderid,$status){

   $order  = OrderStatus::create([
   
   'order_id'  => $orderid->id,
   'status'    => $status, 

   ]);

  return true;

}



public static function CreateMailTemplate($id, $user_data, $subject = '')
{  
    $user_data['BASE_URL']          =   url('/');
    $user_data['LOGO']              =   self::get_settings('logo');
    $user_data['FACEBOOK_ICON']     =   url('public/images/mail/facebook.png');
    $user_data['TWITTER_ICON']      =   url('public/images/mail/twitter.png');
    $user_data['LINKEDIN_ICON']     =   url('public/images/mail/linkedin.png');
    $user_data['INSTAGRAM_ICON']    =   url('public/images/mail/instagram.png');
    $user_data['LOGOICON']          =   url('public/setting/logo-icon.png');
    $user_data['FACEBOOK_LINK']     =   self::get_settings('facebook_link');
    $user_data['TWITTER_LINK']      =   self::get_settings('twitter_link');
    $user_data['LINKEDIN_LINK']     =   self::get_settings('linkedin_link');
    $user_data['INSTAGRAM_LINK']    =   self::get_settings('instagram_link');
    $user_data['BACK_URL']          =   url('/');
    $user_data['SITENAME']          =   self::get_settings('site_name');
    $user_data['COPYRIGHT'] = "&copy; " . date('Y') . "GreenClusta. All Rights Reserved.";
    
    $content_array = array();
    $emailTemplate = EmailTemplateModel::where('id', $id)->where('status', '1')->first();
    $string = "";
    if ($subject == '') {
        $subject = $emailTemplate->subject;
    }
    
    $only_string = '';
    if (isset($emailTemplate)) :
        $keys = [
            '{FIRST_NAME}',
            '{LAST_NAME}',
            '{LINK}',
            '{LINK_1}',
            '{NAME}',
            '{REASON}',
            '{MEDICINE_NAME}',
            '{DOSAGE}',
            '{EMAIL}',
            '{PASSWORD}',
            '{PHONE}',
            '{OTP}',
            '{COUNTRY}',
            '{CATEGORY}',
            '{SUBJECT}',
            '{MESSAGE}',
            '{DATE}',
            '{TIME}',
            '{USERNAME}',
            '{PLAN}',
            '{ADDRESS}',
            '{PHONE}',
            '{ICON}',
            '{LOGO}',
            '{FACEBOOK_ICON}',
            '{TWITTER_ICON}',
            '{LINKEDIN_ICON}',
            '{INSTAGRAM_ICON}',
            '{FACEBOOK_LINK}',
            '{TWITTER_LINK}',
            '{LINKEDIN_LINK}',
            '{INSTAGRAM_LINK}',
            '{COPYRIGHT}',
            '{BASE_URL}',
            '{ROLE}',
            '{REMARK}',
            '{TITLE}',
            '{BACK_URL}',
            '{CODE}',
            '{BILLING_ID}',
            '{PROBLEM}',
            '{DESCRIPTION}',
            '{DATETIME}',
            '{STATUS}',
            '{COMMENT}',
            '{AMOUNT}',
            '{PACKAGE}',
            '{USERTYPE}',
            '{SITENAME}',
            '{ORDER_NUMBER}',
            '{ORDER_AMOUNT}',
            '{LOGOICON}',

        ];
        $only_string = $emailTemplate->body;
        $string  = $emailTemplate->emailHeader->description;
        $string .= $emailTemplate->body;
        $string .= $emailTemplate->emailFooter->description;
    foreach ($keys as $v) :
            $k = str_replace("{", "", $v);
            $k = str_replace("}", "", $k);
            if (isset($user_data[$k])) :
                $string      = str_replace($v, $user_data[$k], $string);
                $only_string = str_replace($v, $user_data[$k], $only_string);
                $subject     = str_replace($v, $user_data[$k], $subject);
    endif;
    endforeach;
    endif;
    
    $content_array = array($subject, $string, $only_string);
    return $content_array;
}


public static function SendMailWithTemplate($replyData = array(), $subject = "", $message = "", $mailids = array(), $attachments = array())
{
    
    $fromData = array( 

        'host'      => self::_get_settings('smtp_host'),
        'port'      => self::_get_settings('smtp_port'),
        'username'  => self::_get_settings('smtp_username'),
        'password'  => self::_get_settings('smtp_password'),
        'from_name' => self::_get_settings('smtp_name'),
        'from_email'=> self::_get_settings('smtp_email'),
    );

 
    $replyToMail = $fromData['username'];
    $replyToName = 'GreenClusta';
    if (isset($replyData['email']) && $replyData['email'] != '') $replyToMail = $replyData['email'];
    if (isset($replyData['name']) && $replyData['name'] != '') $replyToName = $replyData['name'];

    $mail = new PHPMailer();
    $IS_SMTP = 1;
    if ($IS_SMTP):
        $mail->isSMTP(); 
        $mail->CharSet = "utf-8";
        $mail->Host    = self::_get_settings('smtp_host'); 
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
    
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    endif;
    
    $mail->Username = self::_get_settings('smtp_username');
    $mail->Password = "ADEK2027@@@";
    $mail->setFrom(self::_get_settings('smtp_email'), 'GreenClusta');
    
    if ($replyToMail != '') :
        $mail->AddReplyTo($replyToMail, $replyToName);
    endif;
    
    if (isset($attachments) && count($attachments)):
        foreach ($attachments as $key => $value):
            $mail->AddAttachment($value);
        endforeach;
    endif;
    
    $mail->Subject = $subject;
    $mail->MsgHTML($message);
    if (count($mailids)):
        foreach ($mailids as $key => $value):
            $mail->addAddress($key, $value);
        endforeach;
    endif;
    
    $mail->isHTML(true);
    $a = $mail->send();
    return $a;
    
}



    /**
    * Format price to an integer value (e.g., for Stripe).
    * If price is null, return 0.
    *
    * @param float|null $price
    * @return float
    */
    public static function formatPriceToFloat($price)
    {
        if (is_null($price)) {
            return 0;
        }

        return floatval(number_format($price, 2, '.', ''));
    }

     /**
     * Format price to an integer value (e.g., for Stripe).
     * If price is null, return 0.
     *
     * @param float|null $price
     * @return int
     */
    public static function formatPriceToInt($price)
    {
        if (is_null($price)) {
            return 0;
        }
        return intval(number_format($price, 2, '.', ''));
    }



public static function  OrderSuammaryCalculate($productid,$addressid,$courier_partner_id,$couponcode= null){
    

    $shipping_fee          = 0;
    $adminservicefeeamount = 0;
    $coupondiscount_amount = 0;
    $totalorderamount      = 0;
    $discountprice         = 0;
    $finalorderamount      = 0; 
    $message = "Order Summary Created Successfully";    
    $product = Product::find($productid);
    $courier = CourierPartner::find($courier_partner_id);
  
    $totalorderamount = $product->price;
       if($courier->name == 'Uber'){
  
        $delivery   = UberConnectHelper::createDeliveryQuote($productid,$addressid,auth()->user()->id);
        
        try {
            
           $quote_id = $delivery['id'];

        }catch(\Throwable $th) {

            $data = [
                'status' => false, 
                'message'=> "Your address is not deliverable by Uber. Please check and update the address.",
                'code'   => '200'   
            ];
               
               return $data;
             
        }

        try{
  
        $shipping_fee = $delivery['fee'] / 100;
      
        }catch (\Throwable $th) {
      
            $data = [
                'status' => false, 
                'message'=> "We are currently facing some difficulties with this partner. Please try other options.",
                'code'   => '200'  
            ];
        }
      
       }elseif($courier->name == 'USPS'){
      
        $delivery   = USPSHelper::createDeliveryQuote($productid,$addressid,auth()->user()->id);

        if($delivery == false){
            $data = [
                'status' => false, 
                'message'=> "We are currently facing some difficulties with this partner. Please try other options.",
                'code'   => '200'   
            ];
               
               return $data;
             

        }

        $quote_id = null;
        $shipping_fee = $delivery['totalBasePrice'] ?? 0;
                
        if($shipping_fee == 0){

          $data = [
           'status' => false, 
           'message'=> "We are currently facing some difficulties with this partner. Please try other options.",
           'code'   => '200'
          ];
          
          return $data;
        
        }

         
        try {
            
            $quote_id = $delivery['id'];
 
         }catch(\Throwable $th) {
 
            $data = [
                'status' => false, 
                'message'=> "We are currently facing some difficulties with this partner. Please try other options.",
                'code'   => '200'   
            ];
         }


       }else{

        $quote_id = null;
        $shipping_fee = 0;
       
       } 
  
  
       if($couponcode != null){

        $message = "Coupon Applied Successfully";     
        $coupon  = Coupon::where('coupon_code',$couponcode)->first();
   
         if (Carbon::now()->lt(Carbon::parse($coupon->valid_from))){
   
            $data = [
                'status' => false, 
                'message'=> 'This Coupon will start on ' . Carbon::parse($coupon->valid_from)->format('d-m-Y'),
                'code'   => '400'   
            ];
            return $data;
         
         }
         
         if(Carbon::now()->format('Y-m-d') > $coupon->valid_till){
           
            $data = [
                'status' => false, 
                'message'=> 'This Coupon is Expried -'.$coupon->valid_till,
                'code'   => '400'   
            ];
            
            return $data;
         }
   
        if($product->price < $coupon->min_amount){
            
            $data = [
                'status' => false, 
                'message'=> 'Minimum Amount is to use this coupon is '.$coupon->min_amount,
                'code'   => '400'
            ];    
         
            return $data;
       
        }
        
        if(Order::where('coupon_id',$coupon->id)->count() >=  $coupon->no_of_coupon){        
  
            $data = [
                'status' => false, 
                'message'=> 'Number of Coupon limit Reached',
                'code'   => '400'
            ];    
          return $data;
   
        }           

        $discountprice     = $coupon->discount_amount;
        $totalorderamount -= $discountprice; 
      }
  
  
      if(Helper::get_settings("service_fee_commission") > 0 ){
        
       $adminservicefeeamount = ($totalorderamount * Helper::get_settings("service_fee_commission")) / 100;
       $totalorderamount += $adminservicefeeamount;
     
      }
  
       $totalorderamount += $shipping_fee;
       $stripe_fee_percentage = 2.9; 
       $stripe_fixed_fee = 0.30; 
       $stripe_fee = ($totalorderamount * $stripe_fee_percentage / 100) + $stripe_fixed_fee;
       
       $totalorderamount += $stripe_fee;
  
       $sales_tax = ($totalorderamount * Helper::get_settings("sales_tax") / 100);
  
       $totalorderamount += $sales_tax;
  
  
       $data = [
        'quote_id'        => $quote_id,
        'discount_price'   => Helper::formatPriceToFloat($discountprice),        
        'shipping_charges' => Helper::formatPriceToFloat($shipping_fee),
        // 'tax'              => Helper::formatPriceToFloat(Helper::get_settings("service_fee_commission")),
        'service_fee'      => Helper::formatPriceToFloat($adminservicefeeamount),    
        'stripe_platform_fee' => Helper::formatPriceToFloat( $stripe_fee),    
        'sales_tax_amount'    => Helper::formatPriceToFloat( $sales_tax),
        'net_amount'       => Helper::formatPriceToFloat($product->price),    
        'total_amount'     => Helper::formatPriceToFloat($totalorderamount),  
        'status'           =>  true,
 
      ];

      return $data;

}


public static function getStateShortName($stateName) {

    $states = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District Of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
        'PR' => 'Puerto Rico',
    ];


    $stateName = strtolower($stateName);

   
    foreach ($states as $shortName => $fullName) {
        if (strtolower($fullName) === $stateName) {
            return $shortName;
        }
    }

    return "State not found"; 
}




}