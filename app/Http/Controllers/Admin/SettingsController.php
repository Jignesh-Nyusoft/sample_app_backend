<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Validator;
use App\Models\Admin\Settings;
use Storage;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
      return view('admin.settings.site-settings');
    }

    public function store(Request $request)
    {
        $input=$request->all();
    
        $rules['site_name'] 	  = "required";
        $rules['admin_email'] 	= "required|email|max:255|string";
        //$rules['admin_mobile_no'] 	= "required|numeric";

        $errorMsg		= "Oops ! Please fill the required fields.";
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return response()->json(['errorArray'=>$validator->errors(),'error_msg'=>$errorMsg,'slideToTop'=>'yes']);
        }
        else {

          $old_logo = Helper::get_settings('logo'); 
          if ($file = $request->file('logo'))
          {
            $filePath = Helper::UploadImage($request->file('logo'),'setting');      
            $input['logo'] = $filePath;
            if(isset($old_logo))
            {
              //Storage::disk('uploads')->delete($old_logo);
 
            }

          }
          else
          {
            if(isset($old_logo)) {
              $input['logo'] = $old_logo;
            }
          }

          $old_favicon = Helper::get_settings('favicon');

          if ($file = $request->file('favicon'))
          {

            $input['favicon'] = Helper::UploadImage($request->file('favicon'),'setting');
            if(isset($old_favicon))
            {
            //Storage::disk('uploads')->delete($old_favicon);
            }

          }
          else
          {
            if(isset($old_favicon)) {
              $input['favicon'] = $old_favicon;
            }
          }

          $old_footer_logo = Helper::get_settings('footer_logo');

          if ($file = $request->file('footer_logo'))
          {
            $input['footer_logo'] = Helper::UploadImage($request->file('footer_logo'),'setting');

            if(isset($old_footer_logo))
            {
            
            //Storage::disk('uploads')->delete($old_footer_logo);
            }

          }
          else
          {

            if(isset($old_footer_logo)) {
              $input['footer_logo'] = $old_footer_logo;
            }
          
          }

            if(isset($input)){
                foreach($input as $field => $value){
                    if(!in_array($field, ['_method', '_token'])){
                    $setings = Setting::updateOrCreate(
                        ['field' => $field],
                        ['value' => $value]);
                    }
                }
            }

          $output['status']		    = 'success';
          $output['msg']			    = "Site Settings Updated Successfully.";
          $output['msgHead']	    = "Success ! ";
          $output['msgType']	    = "success";
          $output['slideToTop']	  = true;
          return redirect()->back()->with('message','Setting Updated Successfully');

        }
    }

    public function storePayment(Request $request)
    {
        $input=$request->all();

        $rules = array();
        $errorMsg  = "Oops ! Please fill the required fields.";
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return response()->json(['errorArray'=>$validator->errors(),'error_msg'=>$errorMsg,'slideToTop'=>'yes']);
        }
        else {

            if(isset($input) && !empty($input) && !NULL) {
                foreach($input as $field => $value){
                    if(!in_array($field, ['_method', '_token'])){
                      if (isset($value) && !empty($value)) {
                        if ($field == 'publishable_key' || $field == 'secret_key' || $field == 'paypal_client' || $field == 'paypal_secret') {
                          $setings = Setting::updateOrCreate(
                              ['field' => $field],
                              ['value' => base64_encode(Crypt::encryptString($value))]
                          );
                        }
                      }
                     }
                     else {
                      $setings = Setting::updateOrCreate(
                        ['field' => $field], 
                        ['value' => $value],
                      );
                    }
                }
            }

            $output['status']		    = 'success';
            $output['msg']			    = "Payment Settings Updated Successfully.";
            $output['msgHead']	    = "Success ! ";
            $output['msgType']	    = "success";
            $output['slideToTop']	  = true;
            return response()->json($output);
        }
    }

    public function storeOther(Request $request)
    {
        $input=$request->all();

        $rules = array();
      
        $errorMsg  = "Oops ! Please fill the required fields.";
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return response()->json(['errorArray'=>$validator->errors(),'error_msg'=>$errorMsg,'slideToTop'=>'yes']);
        }
        else {

            if(isset($input) && !empty($input) && !NULL) {
                foreach($input as $field => $value){
                    if(!in_array($field, ['_method', '_token'])){
                      if (isset($value) && !empty($value)) {
                          if ($field == 'google_map_key' || $field == 'google_captcha_key' || $field == 'google_captcha_secret' ) {
                            $setings = Setting::updateOrCreate(
                                ['field' => $field],
                                ['value' => base64_encode(Crypt::encryptString($value))]
                              );
                          }
                          else {
                            $setings = Setting::updateOrCreate(
                              ['field' => $field], 
                              ['value' => $value],
                              
                            );
                          }
                        }
                      }
                    }
            }

            $output['status']		    = 'success';
            $output['msg']			    = "Other Settings Updated Successfully.";
            $output['msgHead']	    = "Success ! ";
            $output['msgType']	    = "success";
            $output['slideToTop']	  = true;
            return response()->json($output);
        }
    }

    public function EnvSettingStore(Request $request)
    {
      $input=$request->all();
      $rules = array();
      $errorMsg  = "Oops ! Please fill the required fields.";
      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errorArray'=>$validator->errors(),'error_msg'=>$errorMsg,'slideToTop'=>'yes']);
      }
      else {

          if(isset($input) && !empty($input) && !NULL) {
              foreach($input as $field => $value){
                  if(!in_array($field, ['_method', '_token'])){
                    if (isset($value) && !empty($value)) {
                      if ($field == 'smtp_password') {
                        $setings = Setting::updateOrCreate(
                          ['field' => $field], 
                          ['value' => base64_encode(Crypt::encryptString($value))],
                        );
                      }
                      else {
                        $setings = Setting::updateOrCreate(
                          ['field' => $field], 
                          ['value' => $value],
                        );
                      }
                     
                    }
                  }
              }
          }

          $output['status']		    = 'success';
          $output['msg']			    = "ENV Settings Updated Successfully.";
          $output['msgHead']	    = "Success ! ";
          $output['msgType']	    = "success";
          $output['slideToTop']	  = true;
          return response()->json($output);
      }

    }

}
