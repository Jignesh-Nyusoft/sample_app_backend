<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\User;
use App\Models\Admin;
use Str;
use Illuminate\Support\Facades\Hash;
use Redirect;
use Session;

class LoginController extends Controller
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

    public function login()
    {
        
      return view('admin.login');
    }

    public function loginPost(Request $request)
    {
        
        $rules['email']      = "required|email";
        $rules['password']   = "required";

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $notification = array(
                'message' => 'Oops ! Please check form fields.',
                'alert-type' => 'error'
            );
            return Redirect::route('admin.login')->with($notification);
        }
        else {
            $result = Admin::where('email',$request->email)->first();
            if($result!=null){

                $email              = $request->get('email');
                $password           = $request->get('password');
                
                if($request->get('remember')){
                   $remember = true;
                }
                else {
                    $remember = false;
                }

                $loginCredentials   =[
                    'email'         => $request->get('email'),
                    'password'      => $request->get('password')
                ];
                // echo "Before authenticate"; die;
                $auth = auth()->guard('admin');
                if($auth->attempt($loginCredentials, $remember)) {
                    $urlIntended = Session::get('url.intended');
                    Session::forget('url.intended');
                    if(empty($urlIntended)){
                        $notification = array(
                            'message' => 'Thank-You! You are successfully login.',
                            'alert-type' => 'success'
                    );
                    return Redirect::route('admin.dashboard')->with($notification);
                    
                }else{

                        $notification = array(
                            'message' => 'Something went wrong!',
                            'alert-type' => 'error'
                        );
                        
                        return Redirect::route('admin.login')->with($notification);
                    }

                }
                else {
                    $notification = array(
                        'message'    => 'Sorry! Your login details is not correct, please enter correct email and password.',
                        'alert-type' => 'error'
                    );
                    return Redirect::route('admin.login')->with($notification);
                }
            }
            else {
              $notification = array(
                  'message' => 'Sorry! Sorry! Your login details is not correct, please enter correct email and password.',
                  'alert-type' => 'error'
              );
              return Redirect::route('admin.login')->with($notification);
            }

        }

    }
}
