<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Redirect;

class CourierController extends Controller
{
    public function index(){

        return view("admin.courier.list");

    }

     public function Datatable(Request $request){
        $data = CourierPartner::orderBy('id','DESC');
        return \Yajra\DataTables\DataTables::of($data)
        ->filter(function ($query) use ($request, $data) {
            if ($request->has('slug') && !empty($request->slug)) {
                $query->where(function($q) use ($request, $data) {
                    $q->where('slug', 'like', "%{$request->get('slug')}%");
                });
            }
        })
        
        ->addColumn('status', function ($data) use ($request) {            
            $checked = ($data->status == 'active') ? 'checked' : '';
            $labelText = ($data->status == 'active') ? 'Active' : 'Inactive';
            
            $action = '<div class="custom-control custom-switch">
              <input data-id="' . htmlspecialchars($data->id) . '" type="checkbox" class="custom-control-input" id="customSwitch' . htmlspecialchars($data->id) . '" ' . $checked . '>
              <label class="custom-control-label" for="customSwitch' . htmlspecialchars($data->id) . '">' . $labelText . '</label>
            </div>';            
            return $action;
        })

           
        
        ->rawColumns(['status'])
        ->make(true);
     }


   public function edit($id){

    $data = CourierPartner::where('id',$id)->first();
    return view('admin.courier.create',compact('data'));
   
}


  
   public function create($id=null){
    if($id != null){
       $data = CourierPartner::where('id',$id)->first();
       if($data)
       {
           return view('admin.courier.create',compact('data'));
       }
       else {
           $notification = array(
           'message' => 'Oops! Something went wrong..',
           'alert-type' => 'error'
           );
           return Redirect::route('admin.cms.create')->with($notification);
       }  
    }
     
     return view('admin.courier.create');

    }



    function StatusUpdate(Request $request) {
    
 
        $data = CourierPartner::where('id',$request->id)->first();
      
        if($data->status == 'active'){
             
          $status = 'inactive';
          
        }elseif($data->status == 'inactive'){
          
          $status = 'active';
          
        }
      
        $data->update(['status'=> $status]);
      
      return response()->json(['status'=> 200]);
      
      }

}
