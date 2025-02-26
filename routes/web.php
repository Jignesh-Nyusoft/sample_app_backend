<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\CmsPagescontroller;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ConditionController;
use App\Http\Controllers\Admin\ContactusController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\Faqcontroller;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\SuitableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminCheckMiddleware;
use Illuminate\Support\Facades\Route;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
return view('auth.login');

});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');

});   


Route::get('/forgot-password', function () {
        return view('forgotpassword');
});



Route::post('status-update',[UserController::class,'StatusUpdate'])->name('users.status-update');
Route::name('admin.')->prefix('admin')->group( function () {
    Route::get('/',[AdminController::class,'dashboard'])->name('dashboard');
    Route::get('logout',[AdminController::class,'logout'])->name('logout');
    Route::get('site-settings',[SettingsController::class,'index'])->name('site-settings');
    Route::post('site-settings',[SettingsController::class,'store'])->name('site-settings.store');
    Route::post('site-settings/payment',[SettingsController::class,'storePayment'])->name('site-settings.store.payment');
    Route::post('site-settings/other',[SettingsController::class,'storeOther'])->name('site-settings.store.other');
    Route::post('site-settings/env',[SettingsController::class,'EnvSettingStore'])->name('site-settings.store.env');

Route::prefix('users')->group( function () {
    Route::get('/',[UserController::class,'index'])->name('users');
    Route::get('data/',[UserController::class,'fetchUserData'])->name('users.data');
    Route::get('create',[UserController::class,'create'])->name('users.create');
    Route::post('create',[UserController::class,'store'])->name('users.store');
    Route::get('edit/{id}',[UserController::class,'create'])->name('users.edit');
    Route::post('edit/{id}',[UserController::class,'store'])->name('users.update');
    Route::get('delete/{id}',[UserController::class,'delete'])->name('users.delete');
    Route::get('restore/{id}',[UserController::class,'restore'])->name('users.restore');
    Route::get('change_status/{id?}/{status?}',[UserController::class,'change_status'])->name('users.change_status');
    Route::get('view_user/{id}',[UserController::class,'view_user'])->name('users.view_user');
    Route::post('cropprofile',[UserController::class,'cropProfile'])->name('users.cropprofile');
});
    
    Route::get('faq-index/{id?}',[Faqcontroller::class,'Datatable']);
    Route::prefix('faqs')->group( function () {
        Route::get('/',[Faqcontroller::class,'index'])->name('faqs');
        Route::get('index/',[Faqcontroller::class,'Datatable'])->name('faqs.index');
        Route::get('create',[Faqcontroller::class,'create'])->name('faqs.create');
        Route::post('create',[Faqcontroller::class,'store'])->name('faqs.store');
        Route::get('edit/{id}',[Faqcontroller::class,'create'])->name('faqs.edit');
        Route::post('edit/{id}',[Faqcontroller::class,'store'])->name('faqs.update');
        Route::get('delete/{id}',[Faqcontroller::class,'delete'])->name('faqs.delete');
    });
        
    Route::prefix('email_header_template')->group( function () {
        Route::get('/','App\Http\Controllers\Admin\EmailHeader@index')->name('email_header_template');
        Route::get('/add','App\Http\Controllers\Admin\EmailHeader@Add')->name('email_header_template.add');
        Route::get('/edit/{id}','App\Http\Controllers\Admin\EmailHeader@Edit')->name('email_header_template.edit');
        Route::post('/action/{action}/{_id}', 'App\Http\Controllers\Admin\EmailHeader@Action');
        Route::get('/action/{action}/{_id}', 'App\Http\Controllers\Admin\EmailHeader@Action');
    });
    
    Route::prefix('email_footer_template')->group( function () {
        Route::get('/', 'App\Http\Controllers\Admin\EmailFooter@index')->name('email_footer_template');
        Route::get('/add','App\Http\Controllers\Admin\EmailFooter@Add')->name('email_footer_template.add');
        Route::get('/edit/{id}','App\Http\Controllers\Admin\EmailFooter@Edit')->name('email_footer_template.edit');
        Route::post('/action/{action}/{_id}', 'App\Http\Controllers\Admin\EmailFooter@Action');
        Route::get('/action/{action}/{_id}', 'App\Http\Controllers\Admin\EmailFooter@Action');    
    });
    
    Route::prefix('email_templates')->group( function () {
        Route::get('/','App\Http\Controllers\Admin\EmailTemplates@index')->name('email_templates');
        Route::get('/add','App\Http\Controllers\Admin\EmailTemplates@Add')->name('email_templates.add');
        Route::get('/edit/{id}','App\Http\Controllers\Admin\EmailTemplates@Edit')->name('email_templates.edit');
        Route::post('/action/{action}/{_id}', 'App\Http\Controllers\Admin\EmailTemplates@Action');
        Route::get('/action/{action}/{_id}', 'App\Http\Controllers\Admin\EmailTemplates@Action');
    });
    

});

    #Color
    Route::resource('color', ColorController::class);
    Route::get('color-index',[ColorController::class,'Datatable']);
    Route::get('color/delete/{id?}',[ColorController::class,'destroy']);

    #ContactUs
    Route::resource('contact', ContactusController::class);
    Route::get('contact-index',[ContactusController::class,'Datatable']);
    Route::get('contact/delete/{id?}',[ContactusController::class,'destroy']);

    #Review
    Route::resource('review', ReviewController::class);
    Route::get('review-index',[ReviewController::class,'Datatable']);
    Route::get('review/delete/{id?}',[ReviewController::class,'destroy']);

    #Size
    Route::resource('size', SizeController::class);
    Route::get('size-index',[SizeController::class,'Datatable']);
    Route::get('size/delete/{id?}',[SizeController::class,'destroy']);

    #Condition
    Route::resource('condition', ConditionController::class);
    Route::get('condition-index',[ConditionController::class,'Datatable']);
    Route::get('condition/delete/{id?}',[ConditionController::class,'destroy']);

    #Material
    Route::resource('material', MaterialController::class);
    Route::get('material-index',[MaterialController::class,'Datatable']);
    Route::get('material/delete/{id?}',[MaterialController::class,'destroy']);

    #Suitable
    Route::resource('suitable', SuitableController::class);
    Route::get('suitable-index',[SuitableController::class,'Datatable']);
    Route::get('suitable/delete/{id?}',[SuitableController::class,'destroy']);
    
    #Brand
    Route::resource('brands',BrandController::class);
    Route::any('brand-index',[BrandController::class,'Datatable']);
    Route::get('brands/delete/{id?}',[BrandController::class,'destroy']);
    
    #Banner
    Route::resource('banners',BannerController::class);
    Route::any('banner-index',[BannerController::class,'Datatable']);
    Route::get('banners/delete/{id?}',[BannerController::class,'destroy']);

    #Category
    Route::resource('categories',CategoryController::class);
    Route::get('category-index',[CategoryController::class,'Datatable']);
    Route::get('category/delete/{id?}',[CategoryController::class,'destroy']);
    Route::get('category/subedit/{id?}',[CategoryController::class,'subedit']);



    #SubCategory
    Route::resource('subcategories',SubCategoryController::class);
    Route::get('subcategory-index/{id?}',[SubCategoryController::class,'Datatable']);
    Route::get('category/subcategory/{id?}',[SubCategoryController::class,'index']);
    Route::get('subcategory/delete/{id?}',[SubCategoryController::class,'destroy']);
    Route::get('category/subcategorycreate/{id?}',[SubCategoryController::class,'subcatcreate']);
    

    #CMS
    Route::resource('cms', CmsController::class);
    Route::get('cms-index',[CmsController::class,'Datatable']);
    Route::get('cms/delete/{id?}',[CmsController::class,'destroy']);

    #Courier
    Route::resource('courier', CourierController::class);
    Route::get('courier-index',[CourierController::class,'Datatable']);
    Route::get('courier/delete/{id?}',[CourierController::class,'destroy']);
    Route::post('courier-status-update',[CourierController::class,'StatusUpdate'])->name('CourierStatusUpdate');


    #Product
    Route::resource('product', ProductController::class);
    Route::get('product-index-view',[ProductController::class,'index']);
    Route::get('product-index',[ProductController::class,'Datatable']);
    Route::get('product/delete/{id?}',[ProductController::class,'destroy']);
    Route::get('approve-index',[ProductController::class,'ApproveDatatable']);
    Route::get('product-approve-index',[ProductController::class,'ApproveIndex']);
    Route::get('request-approve/{id?}',[ProductController::class,'ApproveRequest']);
    Route::post('product-status-update',[ProductController::class,'StatusUpdate'])->name('ProductStatusUpdate');

    #Order
    Route::resource('order', OrderController::class);
    Route::get('order-index-view/{type?}',[OrderController::class,'index']);
    Route::get('order-index/{type?}',[OrderController::class,'Datatable']);
    Route::get('order/delete/{id?}',[OrderController::class,'destroy']);
       

    #Coupon
    Route::resource('coupons',CouponController::class);
    Route::any('coupon-index',[CouponController::class,'Datatable']);
    Route::get('coupons/delete/{id?}',[CouponController::class,'destroy']);
    Route::post('profile-update',[UserController::class,'profileUpdate'])->name('ProfileUpdate');

});


require __DIR__.'/auth.php';
