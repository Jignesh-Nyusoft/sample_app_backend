<?php

namespace App\Http\Controllers\Api;
use App\Helpers\EmailHelper;
use App\Helpers\Helper;
use App\Helpers\StripeHelper;
use App\Helpers\TwilioHelper;
use App\Helpers\USPSHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserAddressRequest;
use App\Models\Address;
use App\Models\BusinessProfile;
use App\Models\Order;
use App\Models\OrderReview;
use App\Models\Product;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Repositories\ProductRepository;
use Log;

/**
 * @group Authentication
 *
 * APIs for authentication,Registration and Send Otp and Login With Otp and Profile Update or Get User Profile,get-address-list,store-address,update-address,address-by-id/{id?},
 */
class UserController extends Controller
{

/**
 * Register
 *
 * @header Content-Type application/JsonResponse
 * @bodyParam first_name First  Name required Example: AppUser
 * @bodyParam last_name   Last   Name required Example: AppUser
 * @bodyParam email email required Example: example@gmail.com
 * @bodyParam mobile  required  Example:12345678
 * @bodyParam zip_code Zip Code required Example:313001
 * @bodyParam country_code Country Code required Example:+91
 *  @response {
 *      "status"  : 200,
 *      "message" : "User Register Successfully",
 *      "username": "Ronak data",
 *      "otp"     : "123456",
 *      
 *}
 * 
 */
  public function Registration(RegisterRequest $request){    
    
  $password = Hash::make($request->password);
  $user=  User::create([

      'first_name'      => $request->first_name,
      'last_name'       => $request->last_name, 
      'username'        => $request->username, 
      'mobile'          => $request->mobile,
      'email'           => $request->email,
      'zip_code'        => $request->zip_code,
      'country_code'    => $request->country_code,
      'password'        => $password,
      'is_seller'       => $request->is_seller,
      'is_terms_agreed' => $request->is_terms_agreed ?? 0,
      'is_seller_details_pending' => 1,
    
    ]);

    if($request->is_business_profile == 1){
        BusinessProfile::create([
            'user_id'                => $user->id,
            'business_name'          => $request->business_name,
            'business_email'         => $request->business_email, 
            'business_phone'         => $request->business_phone, 
            'business_country_code'  => $request->business_full_address,
            'full_address'  => $request->email,
            'zip_code'      => $request->business_zip_code,
            'country'       => $request->business_country,
            'state'         => $request->business_state,
            'city'          => $request->business_city,
            'location_lat'  => $request->business_location_lat,
            'location_long' => $request->business_location_long,
          ]);

    }


    $otp =  mt_rand(100000, 999999);
    
    $name    = $user->first_name;
    $email   = $request->email;
    $subject = "Register Successfully";
    $mailData['SUBJECT'] = $subject;
    $mailData['EMAIL']   = $email;
    $mailData['NAME']    = $name;
    $mailData['LINK']    = "";
    $mailData['OTP']     = "";
    $new_email_data = Helper::CreateMailTemplate("21", $mailData, $subject);
    $new_subject    = $subject;
    $new_content    = $new_email_data[1];
    $new_fromdata   = ['email' => $email,'name' => $name];
    $new_mailids    = [$email  => $name];
 
    Helper::SendMailWithTemplate($new_fromdata, $new_subject, $new_content, $new_mailids);
   
    try {
        $stripe = StripeHelper::CreateCustomerAccount($user->first_name,$user->email);
         
        $user->update([
       
            'stripe_customer_id' => $stripe->id, 
        ]);
        
        $stripeid = $stripe->id;
    }catch (\Throwable $th) {
        
     Log::info("Stripe Account not Created for this user $user->id");
     $stripeid = null;
    }

    $data = [
        'username' => $user->first_name,
        'mobile'   => $user->mobile,
        'email'    => $user->email,
        'OTP'      => $otp,
        'stripe_customer_id' => $stripeid,
    
    ];
     
    return Helper::ApiResponse(200,'User Register Successfully',$data);
   }


 /**
 * Send-Otp
 *
 * @header Content-Type application/JsonResponse
 * @bodyParam mobile mobile required Example: 123456789
 * @bodyParam country_code   Country Code required Example: +91
 *  @response {
 *   "status": 200,
 *   "message": "Otp Sent Successfully",
 *  "data": {
 *       "username": "Admin",
 *       "mobile": "7976280868",
 *       "email": "juber.sheikh@nyusoft.com",
 *       "otp"     :"123456",
 *  }
 *  }
 * 
 */

 
public function SendOtp(Request $request){
   


    $request->validate([

    'email'     => 'required|email|exists:users,email', 

    ]);

   
    $user =  User::where('email',$request->email)->first();      
    $otp  =  mt_rand(100000, 999999);
    $response = Helper::SendOtpViaMail($user,$otp);
    
    $user->update(['otp' =>  $otp, 'is_otp_used' => false ,'otp_valid_till' => Carbon::now()->addMinutes(1)]);    
    $user->refresh();

    $data = [
        'username' => $user->first_name,
        'mobile'   => $user->mobile,
        'email'    => $user->email,
        'otp'      => $otp
    ];

    return Helper::ApiResponse(200,'Otp Sent Successfully',$data);
    
}


 /**
 * login-with-otp
 *
 * @header Content-Type application/JsonResponse
 * @bodyParam mobile mobile required Example: 123456789
 * @bodyParam country_code   Country Code required Example: +91
 *  @response {
 *   "status": 200,
 *   "message": "Login Successfully",
 *  "data": {
 * 
 *       "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI0IiwianRpIjoiZWJhOTI5MGEzMTI2OTgwNTZhZjhlMzEwYjEyOGY4NjJmNDQ1ZDgzYWUyY2E0NjUyMWZlZGIwMDg2NjJkMDk5ZWE5MmM0MmViZGFiOGM2Y2YiLCJpYXQiOjE3MjMxMTM3MDQuNDQ0ODA3LCJuYmYiOjE3MjMxMTM3MDQuNDQ0ODA5LCJleHAiOjE3NTQ2NDk3MDQuNDM5MTgsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.tOW6rzqxyAUtHCPSAqNgAL1HiYEeyJEvu5nDqhdbsGrPKkl8TCdcBigZs98PjmQeJOtRpGmfImkABxxrEN8D1ehoOul_MC50ihN9OaKf81ymoR3RkJ2L8ts3qXRhxuZSvaluugZaUgjZAz5YUwCwfkRVfa8Y4w11e5YynYHs84QZtBthXPVazhTSd-bYtkzZSqHRBsPshbZeQIJRHDdcl5y6OP71PvKmzElX1a755v-LPBDPLU4Dc-gtz98DSItgnvce_iLk6ApKtgr0V6rKlFBbFNK3pDaDMoBZhoUv1TvzbPCEVU0eNq0RftuHId-KX37CtKG-VCvhLSrKmbA3qSN2v9XV9D7GP-RZXaQHxK6UnOk7gImP7227XIyYnZhLoHJKIcWGAtpqwYbG7LHI-ibQxxfDmPfXTj9TcB0kV3KOmcW6PzMSLjiR4V45r7q_aMKpti2bUq2DO_oPelpC53Tf669pXCelsXdS9hiHq4Dxjj1zmwARub9Qxzplx5NCu6R0ex6_yJiBIHkqfffOv7xGzQMki_anRdhjqmLxATBBYmqZc6N8PiFC1jC2GwW0Fa3yB0W_o052t3ElMw86lNF_B3PRYTreQhWz1p3Pw6FbxkCBjncnO0F_q-zjO_RERIH0Fy9swD5io4OuG15v__YYGXVKUObMDQN0GtGYjSU
 *  }
 *  }
 * 
 */
public function LoginWithOtp (Request $request){

    $request->validate([

    'mobile'       => 'required|numeric|exists:users,mobile',
    'otp'          => 'required|numeric|min:6',
    
    ]); 

     $user = User::where('mobile',$request->mobile)->first();
     if($user->otp != $request->otp ){

     return Helper::ApiResponse(400,'Otp Dose not  Match Please Check',null);
     
     }

     if($user->is_otp_used == 1){
     
     return Helper::ApiResponse(400,'Otp is Already Used Please try again',null);
        
     }

     if(Carbon::now()->lt(Carbon::parse($user->otp_valid_till)) == false){
       
     return Helper::ApiResponse(400,'Given Otp is Expired Please resend the otp',null);
     
     }

    if($user->status == 'inactive'){
     
    return Helper::ApiResponse(400,'Your Account is Inactive Please Contact Admin For more details',null);
           
    }

    $userData = Auth::loginUsingId($user->id);

    if($userData->status == 'inactive'){
       
     return Helper::ApiResponse(400,'Your Account is not Active, Please contact Admin',null);
        
    }

     $user->update([
        'is_otp_used'  => true,
        'is_verify'    => true, 
        'device_token' => $request->device_token,
    ]);
         
    $request->user()->tokens()->delete();
    $token = $request->user()->createToken($user->id)->accessToken;
 
    $data = ['access_token' => $token];
    return Helper::ApiResponse(200,'Login Successfully',$data);

}


/**
 * Logout
 *
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 *  @response {
 *   "status": 200,
 *    "message": "User logged out successfully",
 *    "data": null
 *}
 * 
 */
 public function Logout(Request $request) {
    
    if (Auth::user()) {
        $request->user()->token()->revoke();

        return Helper::ApiResponse(200, 'User logged out successfully', null);
    
    } else {
        return Helper::ApiResponse(400, 'Something went wrong. Please try again later.', null);
    }
}


  /**
 * My-Profile
 *
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 *  @response{
 *    "status": 200,
 *    "message": "Getting My Profile Data Successfully.",
 *    "data": {
 *        "id": 1,
 *        "first_name": "Admin-check",
 *        "last_name": "Admin",
 *        "country_code": "91",
 *        "mobile": "7976280868",
 *        "email": "juber.sheikh@nyusoft.com",
 *        "role": "Admin",
 *        "gender": null,
 *        "bio": "Test bio",
 *        "status": "active",
 *        "otp": "735213",
 *        "is_verify": 1,
 *        "otp_valid_till": "2024-08-12 05:51:27",
 *        "is_otp_used": 1,
 *        "zip_code": 313001,
 *        "is_online": 1,
 *        "Stripe_connect_ac_id": null,
 *        "is_terms_agreed": 1,
 *        "profile_image": "users/ammTJLaQOo.jpg",
 *        "created_at": null,
 *        "updated_at": "2024-08-12T05:50:44.000000Z",
 *        "deleted_at": null,
 *        "user_image": "http://127.0.0.1:8000/users/ammTJLaQOo.jpg"
 *    }
 *}
 * 
 */
public function MyProfile() {

 $data = User::with('pickpaddress','deliveryaddress')->where('id',Auth::user()->id)->first();
 return Helper::ApiResponse(200, 'Getting My Profile Data Successfully.', $data);
    
}


/**
 * Profile-Update
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 * @bodyParam first_name First  Name required Example: AppUser
 * @bodyParam lst_name   Last   Name required Example: AppUser
 * @bodyParam email email required Example: example@gmail.com
 * @bodyParam mobile  required  Example:12345678
 * @bodyParam zip_code Zip Code required Example:313001
 * @bodyParam country_code Country Code required Example:+91
 * @bodyParam bio BIO  
 * @bodyParam profile_image Profile Image  
 * @bodyParam gender  Gender Example:{'male','female','other'}
 * @bodyParam is_seller  Seller Example:{true,false}
 *  @response{
 *    "status": 200,
 *    "message": "Profile Updated Successfully.",
 *    "data": null
 * 
 *}
 * 
 */
public function UpdateProfile(Request $request){

    $request->validate([
        'first_name'    => 'required|min:2|max:190',
        'last_name'     => 'required|min:2|max:190',
         'mobile'       => 'required|min:9|numeric|unique:users,mobile,' . Auth::id() . ',id',
        'email'         => 'required|email|unique:users,email,' . Auth::id() . ',id',
        'zip_code'      => 'required|min:2|max:15',
        'country_code'  => 'required|max:5',
        'is_seller'     => 'required|in:0,1',
        'profile_image' => 'file|mimes:jpg,jpeg,png|max:3072', 
        'password'         => ['nullable', 'string', 'max:15'], 
        'confirm_password' => ['required_if:password,!=,null', 'same:password'], 
    ]);
 

      $image = null;
      if($request->hasFile('profile_image')){

          $image = Helper::UploadImage($request->file('profile_image'),'users');
      }else{
          $image = Auth::user()->profile_image;
      }

      if(!empty($request->password)){
        $password = Hash::make($request->password);        
      }else{
        $password = auth()->user()->password;
      }

    User::where('id',Auth::id() )->update([
        
        'first_name'    => $request->first_name,
        'last_name'     => $request->last_name,
        'mobile'        => $request->mobile,
        'email'         => $request->email,
        'bio'           => $request->bio,
        'zip_code'      => $request->zip_code,
        'country_code'  => $request->country_code,
        'profile_image' => $image,
        'gender'        => $request->gender, 
        'is_seller'     => $request->is_seller,
        'password'      => $password,
    ]);

    return Helper::ApiResponse(200, 'Profile Updated Successfully.', null);

}



/**
 * Get User All Address
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 *  @response{
 *   "status": 200,
 *   "message": "Getting User Address Successfully.",
 *   "data": [
 *       {
 *           "id": 1,
 *           "user_id": 1,
 *           "zip_code": "313001",
 *           "country": "India",
 *           "state": "Gujrat",
 *           "city": "Rajkot",
 *           "address": null,
 *           "street": null,
 *           "location_lat": null,
 *           "location_long": null,
 *           "status": "active",
 *           "default_address_id": null,
 *           "is_default": 1,
 *           "is_pickup": 1,
 *           "created_at": null,
 *           "updated_at": "2024-08-21T07:54:40.000000Z",
 *           "deleted_at": null
 *       },
 *       {
 *           "id": 2,
 *           "user_id": 1,
 *           "zip_code": "313001",
 *           "country": "India",
 *           "state": "Gujrat",
 *           "city": "Surat",
 *           "address": null,
 *           "street": null,
 *           "location_lat": null,
 *           "location_long": null,
 *           "status": "active",
 *           "default_address_id": null,
 *           "is_default": 0,
 *           "is_pickup": 0,
 *           "created_at": "2024-08-21T06:58:09.000000Z",
 *           "updated_at": "2024-08-21T07:54:40.000000Z",
 *           "deleted_at": null
 *       },
 *    ]
 *}
 * 
 */
public function getAddress(){

  $data = Address::where('user_id',auth()->user()->id)->get();

  return Helper::ApiResponse(200, 'Getting User Address Successfully.', $data);
    
}


/**
 * Store or Update User Address
 * 
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token)
 * 
 * @bodyParam address_id integer The ID of the address to update (if any). Example: 1
 * @bodyParam zip_code string The zip code of the address. required Example: 313001
 * @bodyParam country string The country of the address. required Example: USA
 * @bodyParam state string The state of the address. required Example: California
 * @bodyParam city string The city of the address. required Example: Los Angeles
 * @bodyParam address string The detailed address. required Example: 1234 Elm Street
 * @bodyParam street string The street of the address. Example: Elm Street
 * @bodyParam status string The status of the address (e.g., active, inactive). Example: active
 * @bodyParam is_default boolean Whether this address is the default address. Example: true
 * @bodyParam is_pickup boolean Whether this address is the pickup address. Example: false
 * 
 * @response {
 *    "status": 200,
 *    "message": "Address Created Successfully.",
 *    "data": {
 *       "id": 1,
 *       "user_id": 1,
 *       "zip_code": "313001",
 *       "country": "USA",
 *       "state": "California",
 *       "city": "Los Angeles",
 *       "address": "1234 Elm Street",
 *       "street": "Elm Street",
 *       "status": "active",
 *       "is_default": true,
 *       "is_pickup": false
 *    }
 * }
 * 
 * @response {
 *    "status": 200,
 *    "message": "Address Updated Successfully.",
 *    "data": {
 *       "id": 1,
 *       "user_id": 1,
 *       "zip_code": "313001",
 *       "country": "USA",
 *       "state": "California",
 *       "city": "Los Angeles",
 *       "address": "1234 Elm Street",
 *       "street": "Elm Street",
 *       "status": "active",
 *       "is_default": true,
 *       "is_pickup": false
 *    }
 * }
 */

public function storeAddress(UserAddressRequest $request){

    $userId = Auth::id();
    $hasAddresses = Address::where('user_id', $userId)->exists();

    if ($request->is_default == 1 && $hasAddresses) {
        Address::where('user_id', $userId)->update(['is_default_delivery' => 0]);
    }

    if ($request->is_pickup == 1 && $hasAddresses) {
        Address::where('user_id', $userId)->update(['is_default_pickup' => 0]);
    }
  
    if($hasAddresses == false){
        $is_default_delivery = 1;
        $is_default_pickup   = 1;
    }else{

        $is_default_delivery = $request->is_default;
        $is_default_pickup   = $request->is_pickup;
    }

   $data = Address::updateOrCreate(
 
['id' => $request->address_id],
   
    [
    'user_id'       => Auth::id(),
    'zip_code'      => $request->zip_code,
    'country'       => $request->country,
    'mobile'        => $request->mobile,
    'country_code'  => $request->country_code,
    'state'         => $request->state,
    'city'          => $request->city,
    'address'       => $request->address,
    'street'        => $request->street,
    'status'        => $request->status ?? 'active',
    'location_lat'  => $request->location_lat,
    'location_long'       => $request->location_long,     
    'is_default_delivery' => $is_default_delivery,
    'is_default_pickup'   => $is_default_pickup,
    'house_no'            => $request->house_no,
   ]);

   $message = $data->wasRecentlyCreated ? 'Address Created Successfully.' : 'Address Updated Successfully.';
   return Helper::ApiResponse(200, $message, $data);

}


/**
 * Address-By-Id
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 * @urlParam id required The ID of the Address-id.URL/address-by-id/1
 * @response{
 *    "status": 200,
 *    "message": "Getting Address Data Successfully.",
 *    "data": {
 *        "id": 1,
 *        "user_id": 1,
 *        "zip_code": "313001",
 *        "country": "India",
 *        "state": "Gujrat",
 *        "city": "Rajkot",
 *        "address": null,
 *        "street": null,
 *        "location_lat": null,
 *        "location_long": null,
 *        "status": "active",
 *        "default_address_id": null,
 *        "is_default": 1,
 *        "is_pickup": 1,
 *        "created_at": null,
 *        "updated_at": "2025-08-21T07:54:40.000000Z",
 *        "deleted_at": null
 *    }
 *}
 * 
 */

public function getAddressById($id = null){
    
    if(Address::where('id', $id)->where('user_id',Auth::id())->exists()){

        $data = Address::find($id);
        return Helper::ApiResponse(200, 'Getting Address Data Successfully.', $data);           
    }

     return Helper::ApiResponse(400, 'Something Went Wrong.', null);


}


/**
* Get Seller Details
*
* Retrieve details of a seller including their basic information, products, and reviews.
* 
* @urlParam id required The ID of the seller. Example: 1
* 
* @response {
*    "status": 200,
*    "message": "Getting Seller Details Successfully.",
*    "data": {
*        "seller": {},
*        "product": {
*            "current_page": 1,
*            "data": [
*                {
*                    "id": 1,
*                    "product_name": "sit",
*                    "slug": null,
*                    "user_id": 1,
*                    "category_id": 1,
*                    "brand_id": 50,
*                    "size_id": 2,
*                    "material_id": 1,
*                    "color_id": 3,
*                    "condition_id": 2,
*                    "suitable_id": 10,
*                    "description": "Modi molestiae rem quia voluptas hic sed. Veritatis voluptate vitae quaerat cupiditate sit labore consequatur. Aut dignissimos et et qui maxime omnis enim cumque.",
*                    "cloth_type": "old",
*                    "stock": 17,
*                    "price": 448.04,
*                    "image": "https://via.placeholder.com/640x480.png/008800?text=tempora",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-07T10:05:30.000000Z",
*                    "updated_at": "2024-08-07T10:05:30.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": false,
*                    "is_wishlist": true,
*                    "product_image": "https://via.placeholder.com/640x480.png/008800?text=tempora"
*                },
*                {
*                    "id": 46,
*                    "product_name": "a",
*                    "slug": null,
*                    "user_id": 1,
*                    "category_id": 2,
*                    "brand_id": 2,
*                    "size_id": 2,
*                    "material_id": 1,
*                    "color_id": 1,
*                    "condition_id": 1,
*                    "suitable_id": null,
*                    "description": "Omnis eveniet et in asperiores. Repellendus voluptates veniam qui accusantium. Ut sequi eveniet culpa.",
*                    "cloth_type": "old",
*                    "stock": 49,
*                    "price": 228.78,
*                    "image": "https://via.placeholder.com/640x480.png/0099ee?text=molestias",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-07T10:05:30.000000Z",
*                    "updated_at": "2024-08-26T05:22:39.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": false,
*                    "is_wishlist": false,
*                    "product_image": "https://via.placeholder.com/640x480.png/0099ee?text=molestias"
*                },
*                {
*                    "id": 47,
*                    "product_name": "cumque",
*                    "slug": null,
*                    "user_id": 1,
*                    "category_id": 2,
*                    "brand_id": 2,
*                    "size_id": 1,
*                    "material_id": 1,
*                    "color_id": 1,
*                    "condition_id": 1,
*                    "suitable_id": null,
*                    "description": "Velit sequi nihil quod vel repellendus. Quia dolorum vel consequatur rerum cupiditate ut. Necessitatibus ea accusantium ducimus omnis ipsa. Necessitatibus commodi et qui veritatis.",
*                    "cloth_type": "new",
*                    "stock": 59,
*                    "price": 219,
*                    "image": "https://via.placeholder.com/640x480.png/00dd11?text=et",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-07T10:05:30.000000Z",
*                    "updated_at": "2024-08-08T05:16:11.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": false,
*                    "is_wishlist": false,
*                    "product_image": "https://via.placeholder.com/640x480.png/00dd11?text=et"
*                },
*                {
*                    "id": 48,
*                    "product_name": "soluta",
*                    "slug": null,
*                    "user_id": 1,
*                    "category_id": 1,
*                    "brand_id": 1,
*                    "size_id": 2,
*                    "material_id": 1,
*                    "color_id": 2,
*                    "condition_id": 2,
*                    "suitable_id": null,
*                    "description": "Laudantium in fugit laudantium quia deleniti eos et neque. Corporis quidem iusto voluptate culpa doloremque sunt. Sint maiores fugiat eaque voluptas facere voluptas autem. Eaque iste quia iure.",
*                    "cloth_type": "new",
*                    "stock": 69,
*                    "price": 314.73,
*                    "image": "https://via.placeholder.com/640x480.png/00bb55?text=quo",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-07T10:05:30.000000Z",
*                    "updated_at": "2024-08-08T05:03:13.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": false,
*                    "is_wishlist": false,
*                    "product_image": "https://via.placeholder.com/640x480.png/00bb55?text=quo"
*                },
*                {
*                    "id": 49,
*                    "product_name": "ipsa",
*                    "slug": null,
*                    "user_id": 1,
*                    "category_id": 1,
*                    "brand_id": 1,
*                    "size_id": 1,
*                    "material_id": 2,
*                    "color_id": 1,
*                    "condition_id": 2,
*                    "suitable_id": null,
*                    "description": "Voluptatibus aperiam adipisci voluptas. Dolor quidem qui quia et blanditiis quis labore consectetur. Perspiciatis qui corrupti quam qui magnam tempora est fugit. Maiores ducimus explicabo ex.",
*                    "cloth_type": "old",
*                    "stock": 34,
*                    "price": 216.02,
*                    "image": "https://via.placeholder.com/640x480.png/0011aa?text=ullam",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-07T10:05:30.000000Z",
*                    "updated_at": "2024-08-09T12:05:44.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": false,
*                    "is_wishlist": false,
*                    "product_image": "https://via.placeholder.com/640x480.png/0011aa?text=ullam"
*                },
*                {
*                    "id": 50,
*                    "product_name": "molestiae",
*                    "slug": null,
*                    "user_id": 1,
*                    "category_id": 1,
*                    "brand_id": 1,
*                    "size_id": 1,
*                    "material_id": 1,
*                    "color_id": 1,
*                    "condition_id": 2,
*                    "suitable_id": null,
*                    "description": "Fugit sit numquam doloremque. Sunt omnis aut sit et explicabo unde expedita. Est numquam minima est ex.",
*                    "cloth_type": "old",
*                    "stock": 24,
*                    "price": 93.04,
*                    "image": "https://via.placeholder.com/640x480.png/000033?text=sed",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-07T10:05:30.000000Z",
*                    "updated_at": "2024-08-14T03:32:38.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": false,
*                    "is_wishlist": true,
*                    "product_image": "https://via.placeholder.com/640x480.png/000033?text=sed"
*                },
*                {
*                    "id": 115,
*                    "product_name": "APi",
*                    "slug": "api",
*                    "user_id": 1,
*                    "category_id": 1,
*                    "brand_id": 1,
*                    "size_id": 1,
*                    "material_id": 1,
*                    "color_id": 1,
*                    "condition_id": 2,
*                    "suitable_id": 10,
*                    "description": "test product",
*                    "cloth_type": "new",
*                    "stock": 10,
*                    "price": 150,
*                    "image": "product/JOpTsNWi9O.png",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-23T11:01:09.000000Z",
*                    "updated_at": "2024-08-26T06:44:39.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": true,
*                    "is_wishlist": false,
*                    "product_image": "http://127.0.0.1:8000/product/JOpTsNWi9O.png"
*                },
*                {
*                    "id": 118,
*                    "product_name": "APi",
*                    "slug": "api",
*                    "user_id": 1,
*                    "category_id": 1,
*                    "brand_id": 1,
*                    "size_id": 1,
*                    "material_id": 1,
*                    "color_id": 1,
*                    "condition_id": 2,
*                    "suitable_id": 10,
*                    "description": "test product",
*                    "cloth_type": "new",
*                    "stock": 10,
*                    "price": 150,
*                    "image": "product/sIktBmdYYb.png",
*                    "is_approved": 1,
*                    "status": "active",
*                    "created_at": "2024-08-23T11:01:52.000000Z",
*                    "updated_at": "2024-08-26T06:42:38.000000Z",
*                    "deleted_at": null,
*                    "is_fresh": true,
*                    "is_wishlist": false,
*                    "product_image": "http://127.0.0.1:8000/product/sIktBmdYYb.png"
*                }
*            ],
*            "first_page_url": "http://127.0.0.1:8000/api/seller-details/1?page=1",
*            "from": 1,
*            "last_page": 1,
*            "last_page_url": "http://127.0.0.1:8000/api/seller-details/1?page=1",
*            "links": [
*                {
*                    "url": null,
*                    "label": "&laquo; Previous",
*                    "active": false
*                },
*                {
*                    "url": "http://127.0.0.1:8000/api/seller-details/1?page=1",
*                    "label": "1",
*                    "active": true
*                },
*                {
*                    "url": null,
*                    "label": "Next &raquo;",
*                    "active": false
*                }
*            ],
*            "next_page_url": null,
*            "path": "http://127.0.0.1:8000/api/seller-details/1",
*            "per_page": 10,
*            "prev_page_url": null,
*            "to": 8,
*            "total": 8
*        },
*        "reviews": {
*            "current_page": 1,
*            "data": [
*                {
*                    "id": 1,
*                    "order_id": 1,
*                    "order_item_id": 1,
*                    "product_id": 118,
*                    "user_id": 1,
*                    "seller_id": 1,
*                    "review": "best product from seller",
*                    "rating": 5,
*                    "created_at": null,
*                    "updated_at": null,
*                    "review_images": []
*                }
*            ],
*            "first_page_url": "http://127.0.0.1:8000/api/seller-details/1?page=1",
*            "from": 1,
*            "last_page": 1,
*            "last_page_url": "http://127.0.0.1:8000/api/seller-details/1?page=1",
*            "links": [
*                {
*                    "url": null,
*                    "label": "&laquo; Previous",
*                    "active": false
*                },
*                {
*                    "url": "http://127.0.0.1:8000/api/seller-details/1?page=1",
*                    "label": "1",
*                    "active": true
*                },
*                {
*                    "url": null,
*                    "label": "Next &raquo;",
*                    "active": false
*                }
*            ],
*            "next_page_url": null,
*            "path": "http://127.0.0.1:8000/api/seller-details/1",
*            "per_page": 10,
*            "prev_page_url": null,
*            "to": 1,
*            "total": 1
*        }
*    }
*}
* */

public function sellerDetails($id = null){
    
  $data = [
           'seller' => User::with('pickpaddress')->where('id',$id)-> select('id','first_name','last_name','bio')->first(),
           'product'=> Product::where('user_id',$id)->where(['is_approved'=>1,'status' => 'active'])->paginate(10),
           'reviews'=> OrderReview::with('ReviewImages','ReviewGivenByUser')->where('seller_id',$id)->paginate(10),     
          ];

  return Helper::ApiResponse(200, 'Getting Seller Details Successfully.',$data);

}


public function deleteAccount(){      
//$product = Product::where('user_id',auth()->user()->id());
//if($product->exists()){
//$product->delete();
//}
      
User::where('id',Auth::id())->update([
    'status'  => 'inactive'
  ]);

  return Helper::ApiResponse(200, 'User Account Deleted Successfully.',null);

}

public function addressDelete($id){
    
$address = Address::find($id);

if($address->is_default_delivery == 1){

return Helper::ApiResponse(400, 'Can not Delete Default Delivery Address.',null);

}

if($address->is_default_pickup == 1){

return Helper::ApiResponse(400, 'Can not Delete Default Pickup Address.',null);

}

$address->delete();
 
return Helper::ApiResponse(200, 'Address Deleted Successfully',null);

}


public function becomeSeller(Request $request){
    
  $request->validate([
    // 'stripe_connect_ac_id' => 'required',
    'pickup_address_id'    => 'required|exists:addresses,id'
  ]);  
 

$user = User::where('id',auth()->user()->id)->first();

$user->update([
'is_seller'  => 1,
'stripe_connect_ac_id' => $request->Stripe_connect_ac_id

]);

Address::where(['user_id'=>auth()->user()->id,'id' => $request->pickup_address_id])->update([

'is_default_pickup' => 1

]);

$name    = $user->first_name;
$email   = $user->email;
$subject = "Upgraded to a Seller account";
$mailData['SUBJECT'] = $subject;
$mailData['EMAIL']   = $email;
$mailData['NAME']    = $name;
$mailData['LINK']    = "";
$mailData['ORDER_NUMBER'] = null;
$mailData['ORDER_AMOUNT'] = null;
$new_email_data = Helper::CreateMailTemplate("20", $mailData, $subject);
$new_subject    = $subject;
$new_content    = $new_email_data[1];
$new_fromdata   = ['email' => $email,'name' => $name];
$new_mailids    = [$email  => $name];

Helper::SendMailWithTemplate($new_fromdata, $new_subject, $new_content, $new_mailids);
return Helper::ApiResponse(200, 'Seller Account Created Successfully',null);

}


public function changeSellerPickupAddress($id){

    if(empty($id) || Address::where('id',$id)->doesntExist()){
 
     return Helper::ApiResponse(200, 'Address is not Valid',null);
    
    }
     
    if(Address::where('user_id',auth()->user()->id)->exists()){
       
        Address::where('user_id',auth()->user()->id)->update([
        'is_default_pickup'   => 0,
        ]);
    
}
    
    Address::where('id',$id)->update([

    'is_default_pickup'   => 1,

    ]);

    return Helper::ApiResponse(200, 'Seller Pickup Address Changed Successfully',null);

}


public function LoginWithPassword(Request $request)
{
    
    $request->validate([
        'email'    => 'required|max:255',
        'password' => 'required',
    ]);


    $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    $user = User::where($loginType, $request->email)->first();
   
    if (!$user) {
        return Helper::ApiResponse(404, 'User not found.', null);
    }

    
    if (!Hash::check($request->password, $user->password)) {
        return Helper::ApiResponse(401, 'Invalid credentials.', null);
    }

    if ($user->is_verify == 0) {
        $data = ['is_verify' => 0];
        return Helper::ApiResponse(200, 'Your Email Is not verfied', $data);
    }

    $user->update([
        'is_otp_used'  => true, 
        'device_token' => $request->device_token,
    ]);
    $user->tokens()->delete();
    $token = $user->createToken($user->id)->accessToken;
 
    $data = ['access_token' => $token];
    return Helper::ApiResponse(200,'Login Successfully',$data);

}



public function ChangePassword(Request $request) {
    
  $request->validate([
    'old_password'     => 'required',
    'password'         => 'required',  
  ]);

  $user = User::find(auth()->user()->id);  
  if (!Hash::check($request->old_password, $user->password)) {
    return Helper::ApiResponse(400, 'Old Password does not match', null);
  }

  $password = Hash::make($request->password);
  $user->update([
  
  'password' => $password, 
  
  ]);



  Helper::SendChangePasswordViaMail($user);
  return Helper::ApiResponse(200,'Password Changed Successfully',null);

}


public function ForgotPassword(Request $request) {
  
   $request->validate([
   'otp'        => 'required',
   'email'      => 'required',
   'password'         => ['nullable', 'string', 'max:15'], 
   'confirm_password' => ['required_if:password,!=,null', 'same:password'], 

   ]);


     $user = User::where('email',$request->email)->first();
     if($user->otp != $request->otp ){

     return Helper::ApiResponse(400,'Otp Dose not  Match Please Check',null);
     
     }

     if($user->is_otp_used == 1){
     
     return Helper::ApiResponse(400,'Otp is Already Used Please try again',null);
        
     }

     if(Carbon::now()->lt(Carbon::parse($user->otp_valid_till)) == false){
       
     return Helper::ApiResponse(400,'Given Otp is Expired Please resend the otp',null);
     
     }

    $password = Hash::make($request->password);
    $user->update([
    'password' => $password
    ]);


    return Helper::ApiResponse(200,'Password Changed Successfully',null);
    
}


public function VerifyEmail(Request $request) {
    
$request->validate([
 
    'email' => 'required|exists:users,email',
    'otp'   => 'required', 

]);

 if(User::where('email',$request->email)->exists()){

    $user = User::where('email',$request->email)->first();

    if($user->otp != $request->otp ){
        return Helper::ApiResponse(400,'otp does not match',null);
        
    }

    $user->update([
    'is_verify' => 1,
    'is_otp_used' => 1,
    'device_token' => $request->device_token,
    ]);


    $user->tokens()->delete();
    $token = $user->createToken($user->id)->accessToken;
 
    $data = ['access_token' => $token];
    return Helper::ApiResponse(200,'Email Verify Successfully',$data);
 }


 return Helper::ApiResponse(200,'user not found',null);

}

}
