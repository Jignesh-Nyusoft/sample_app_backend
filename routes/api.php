<?php

use App\Helpers\USPSHelper;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/*
Project - GreenClusta Api->V1
*/

#require base_path('routes/api/v1.php');


#Authentication API
Route::post('register',[UserController::class,'Registration']);
Route::post('send-otp',[UserController::class,'SendOtp']);
Route::post('login-with-otp',[UserController::class,'LoginWithOtp']);


Route::get('auth/{social?}/callback', [UserController::class, 'ScocialCallback']);
Route::get('auth/{social?}/redirect', [UserController::class, 'HandelSocialRedirect']);
Route::post('login-with-password',[UserController::class,'LoginWithPassword']);
Route::post('verify-email',[UserController::class,'VerifyEmail']);
Route::post('forgot-password',[UserController::class,'ForgotPassword']);

Route::get('logout',[UserController::class,'Logout'])->middleware('auth:api');
Route::get('my-profile',[UserController::class,'MyProfile'])->middleware('auth:api');
Route::post('update-profile',[UserController::class,'UpdateProfile'])->middleware('auth:api');
Route::post('contact-us',[CommonController::class,'contactusStore']);
Route::post('order-payment-success-webhook',[OrderController::class,'orderPaymentWebhook']);
Route::post('order-payment-canceled-webhook',[OrderController::class,'orderPaymentCanceledWebhook']);
Route::post('uber-delivery-status',[CommonController::class,'uberWebhook']);
Route::post('handel-USPS-status',[CommonController::class,'HandelUSPSStatus']);


#Stripe Connect Account callback
Route::get('handel-stripe-account-callback', [CommonController::class, 'handleConnectCallback']);
Route::group(['middleware' => 'auth:api'], function () {
Route::post('change-password',[UserController::class,'ChangePassword']);
Route::get('delete-my-account',[UserController::class,'deleteAccount']);
Route::post('stripe-account-verification', [CommonController::class, 'CheckExistingStripeConnectAccount']);
Route::get('Courier-partner-List', [CommonController::class, 'CourierpartnerList']);

#User Related Apis    
Route::get('get-address-list',[UserController::class,'getAddress']);
Route::post('store-address',[UserController::class,'storeAddress']);
Route::post('update-address',[UserController::class,'storeAddress'])->name('update-address');

Route::get('address-by-id/{id?}',[UserController::class,'getAddressById']); 
Route::get('address-delete/{id?}',[UserController::class,'addressDelete']);

#Category API
Route::get('get-categories',[CategoryController::class,'getCategory']);
Route::get('get-subcategories/{id?}',[CategoryController::class,'getSubCategory']);
Route::get('category-by-id/{id?}',[CategoryController::class,'getCategoryById']);

#Suitable Master Api
Route::post('get-suitable',[CommonController::class,'getSuitable']);
Route::get('get-suitable-list',[CommonController::class,'getSuitableList']);

#Wishlist Api
Route::get('get-wishlist',[WishlistController::class,'getWishlist']);
Route::post('add-to-wishlist',[WishlistController::class,'addToWishlist']);
Route::get('remove-wishlist/{id?}',[WishlistController::class,'removeWishlist']);

#Product
Route::get('product-attribute-list/{id?}',[ProductController::class,'productAttributesList']);
Route::post('product-filter',[ProductController::class,'productFilter']);
Route::get('product-by-id/{id?}',[ProductController::class,'productById']);
Route::post('product-store',[ProductController::class,'productStore']);
Route::post('product-update',[ProductController::class,'productUpdate']);
Route::get('product-image-delete/{id?}',[ProductController::class,'productImageDelete']);
Route::get('product-delete/{id?}',[ProductController::class,'productDelete']);
Route::get('my-products/{type?}',[ProductController::class,'myProduct']);   
Route::get('product-courier-delete/{id?}',[ProductController::class,'ProductCourierDelete']);   

#Banner Api
Route::get('get-banners',[CommonController::class,'getBanner']);

#CMS
Route::get('get-cms/{slug?}',[CommonController::class,'getCMS']);

#Order
Route::post('order-create',[OrderController::class,'orderCreate']);
Route::post('order-checkout',[OrderController::class,'orderCheckout']);
Route::post('my-purchase-order/{status?}',[OrderController::class,'purchaseOrderList']);
Route::get('order-by-id/{id?}',[OrderController::class,'orderById']);
Route::get('order-markas-deliverd/{order_id?}/{type?}',[OrderController::class,'OrderMarkasDeliverd']);


#Transctions
Route::get('transction-history',[OrderController::class,'transctionHistory']);

#Seller related apis
Route::get('seller-details/{id?}',[UserController::class,'sellerDetails']);
Route::post('become-seller',[UserController::class,'becomeSeller']);
Route::get('change-seller-pickup/{address_id?}',[UserController::class,'changeSellerPickupAddress']);

#Coupon Api
Route::post('apply-coupon',[CommonController::class,'applyCoupon']);
Route::get('get-coupons',[CommonController::class,'getCouponList']);

#Review 
Route::post('create-review',[ReviewController::class,'Store']);
Route::get('review-image-delete/{id?}',[ReviewController::class,'reviewImageDelete']);

#Contact Us
Route::get('contact-info',[CommonController::class,'contactUS']); 
//Route::get('contact-us',[CommonController::class,'contactUS']);

#Stripe Payment Gateway
Route::post('create-account',[StripePaymentController::class,'createConnectAccount']);
Route::get('get-card-list',[StripePaymentController::class,'savedCardList']);
Route::get('set-card-default/{card_id?}',[StripePaymentController::class,'setCardDefault']);
Route::get('delete-card/{card_id?}',[StripePaymentController::class,'deleteStripeCard']);
Route::get('get-default-card/{card_id?}',[StripePaymentController::class,'getDefaultCard']);
Route::get('Create-Intent',[StripePaymentController::class,'createIntent']);
Route::get('generate-connect-link',[StripePaymentController::class,'generateConnectLink']);

#Notification
Route::get('get-notifications',[CommonController::class,'getNotification']);
Route::get('read-notifications/{id?}',[CommonController::class,'readNotification']);
Route::get('delete-notifications/{id?}',[CommonController::class,'deleteNotification']);

#Chat 
Route::post('chat-image-store',[CommonController::class,'chatImageStore']);

#Uber Connect Apis
Route::post('check-availability',[CommonController::class,'checkDeliveryAvailability']);
Route::get('check-delivery-status/{order_id?}',[CommonController::class,'checkDeliveryStatus']);

});


