<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Storage;
use Redirect;
use Str;
use View;
use Mail;
use App\Mail\SendMarkdownMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request as Request2;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{   
    public function index()
    {
        $count = User::orderBy('id','DESC')->count();
        return view('admin.users.list',compact('count'));
    }


    public function fetchUserData(Request $request)
    {
        
        $data = DB::table('users')->orderBy('id','DESC')->get();
    
        return DataTables::of($data)

        ->addColumn('status', function ($data) use ($request) {            
            
            $checked = ($data->status == 'active') ? 'checked' : '';
            $labelText = ($data->status == 'active') ? 'Active' : 'Inactive';
            
            $action = '<div class="custom-control custom-switch">
            <input data-id="' . htmlspecialchars($data->id) . '" type="checkbox" class="custom-control-input" id="customSwitch' . htmlspecialchars($data->id) . '" ' . $checked . '>
            <label class="custom-control-label" for="customSwitch' . htmlspecialchars($data->id) . '">' . $labelText . '</label>
            </div>';
            
            return $action;
        })
        
        ->addColumn('action', function ($data) {
            
        $action = '<a href="'. route('admin.users.edit',['id'=>$data->id]) .'" class="btn btn-primary" title="Edit User"><i class="fa fa-edit"></i></a>';
        $action .='<button data-url="'.route('admin.users.delete',$data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete User" data-name="'.$data->first_name.'"><i class="fa fa-trash"></i></button>';
                    
        return $action;

        })
        ->rawColumns(['action','status'])
        ->make(true);

    }

    public function create($id=null)
    {
        
        if($id!=null)
        {
            $data = User::select('*')->where('id',$id)->first();
            if($data)
            {
                return view('admin.users.create',compact('data'));
            }
            else {
                $notification = array(
                    'message' => 'Oops! Something went wrong..',
                    'alert-type' => 'error'
                );
                return Redirect::route('admin.users')->with($notification);
            }
        }
        return view('admin.users.create');
    }


    public function store(Request $request, $id=null)
    {

        $request->validate([
        'first_name'   => 'required',
        'last_name'    => 'required',
        'email'        => 'required|unique:users,email,'.$id,
        'mobile'       => 'required',
        'Country_code' => 'required'    
        ]);


                $name = $request->first_name." ".$request->last_name;
                $slug = Str::slug($name);
                $input['slug'] = $slug;
                if(isset($input['password'])){
                    $input['password'] = Hash::make($input['password']);
                }
               
                $old_profile_image = null;
                if(isset($id)) {
                    $detail = User::withTrashed()->find($id); 
                    $old_profile_image = $detail->profile_image ?? null;
                }
                
                if ($request->hasFile('profile_image')) {
                    $input['profile_image'] = Helper::UploadImage($request->file('profile_image'), 'user');
                } else {
                    $input['profile_image'] = $old_profile_image;
                }
                



                if(!isset($id))
                {
                    $str = 
                    "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
                    $generatePassword = $this->get_password($str, 8);
                    $input['password'] = Hash::make($generatePassword); 
                }
                if(isset($request->id) && !empty($request->id)){

                   user::where('id', $request->id)->update([
                    'first_name'   => $request->first_name,
                    'last_name'    => $request->last_name,
                    'country_code' => $request->Country_code,
                    'bio'          => $request->bio,
                    'email'        => $request->email,
                    'mobile'       => $request->mobile,
                    'status'       => $request->status ?? 'active',
                    'gender'       => $request->gender,
                    'profile_image'=>  $input['profile_image'],
                   
                ]);
                  
                    
                }else{
                    
                    user::updateOrCreate(['id'=>$id],[ 
                        'first_name'   => $request->first_name,
                        'last_name'    => $request->last_name,
                        'country_code' => $request->Country_code,
                        'bio'          => $request->bio,
                        'email'        => $request->email,
                        'mobile'       => $request->mobile,
                        'status'       => $request->status ?? 'active',
                        'gender'       => $request->gender,
                        'password'     => $input['password'], 
                        'profile_image'=>  $input['profile_image'],
                       ]);
                   
                }

                $output['status'] = 'success';
      
                $output['msg'] = "User updated successfully.";
                $notification  = array(
                    'message'    => $output['msg'],
                    'alert-type' => $output['status'],
                );
                return Redirect::route('admin.users')->with($notification);
    }

    public function delete($id)
    {
        $detail = User::select('*')->where('id',$id)->withTrashed()->first();
        $subject = "Account deleted!";
        $message = "Your account has been deleted by admin.";
        if(isset($detail) && !empty($detail))
        {
            $detail->delete();

            $notification = array(
                'message' => 'User deleted successfully.',
                'alert-type' => 'success'
            );
        }
        else{
            $notification = array(
                'message' => 'Oops! Something went wrong.',
                'alert-type' => 'error'
            );
        }

        return Redirect::back()->with($notification);
    }

    public function restore($id)
    {
        $detail = User::select('*')->where('id',$id)->withTrashed()->first();

        $detail['is_active'] = 1;
        $detail->save();

        if($detail)
        {
            $detail->restore();
            $notification = array(
                'message'    => 'User restored successfully.',
                'alert-type' => 'success'
            );
        }
        else
        {
            $notification = array(
                'message'    => 'Oops! Something went wrong.',
                'alert-type' => 'error'
            );
        }

        return Redirect::back()->with($notification);
    }

    function get_password($str, $len = 0) { 

        $pass = "";
        $str_length = strlen($str);
        if($len == 0 || $len > $str_length){
            $len = $str_length;
        }
        for($i = 0;  $i < $len; $i++){

            $pass .=  $str[rand(0, $str_length - 1)];
        }
        return $pass;
    }
    
   public function change_status($id = null,$status = null){
         
        $data = User::where('id',$id)->update(['status' => $status]);

        $notification = array(
            'message' => 'Status Updated Suceesfully.',
            'alert-type' => 'success'
        );
        return Redirect::back()->with($notification);
    }


function StatusUpdate(Request $request) {
    
 
    $data = User::where('id',$request->id)->first();

    if($data->status == 'active'){
        $status = 'inactive';
    }elseif($data->status == 'inactive'){
        $status = 'active';
    }

    $data->update(['status'=> $status]);

return response()->json(['status'=> 200]);

}



public function profileUpdate(ProfileRequest $request)
{

    $user = User::find(auth()->user()->id);
    $image = null;
    if($request->hasFile('image')){

        $image = Helper::UploadImage($request->file('image'),'users');
    }else{
        $image = $user->profile_image;
    }

 $user->update([
  
  'first_name'   => $request->first_name,
  'last_name'    => $request->last_name,  
  'country_code' => $request->country_code,     
  'bio'          => $request->bio,       
  'mobile'       => $request->mobile,       
  'email'        => $request->email,       
  'profile_image'=> $image,   

]);

$user->refresh();

$notification = [
    'message'    => "Profile Updated Successfully",
    'alert-type' => 'success',
];    
    return  Redirect::back()->with($notification);
}



}
