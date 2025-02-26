<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Contactus;
use App\Models\User;
use Illuminate\Http\Request;
use Redirect;
use Yajra\DataTables\DataTables;

class ContactusController extends Controller
{
    public function index(){
       
        return view("admin.contact.list");

    }

     public function Datatable(Request $request){

        $data = Contactus::orderBy('id','DESC');
        return DataTables::of($data)
        ->filter(function ($query) use ($request, $data) {
            if ($request->has('slug') && !empty($request->slug)) {
                $query->where(function($q) use ($request, $data) {
                    $q->where('slug', 'like', "%{$request->get('slug')}%");
            });
            }
        })

        ->addColumn('user', function ($data) use ($request){      

            if($data->user_id != null){
                $user = User::find($data->user_id); 
                $name = $user->first_name;     
            }else{
                $name = 'No Data';
            } 
            
             return $data->first_name;
        })

        ->addColumn('action', function ($data) {
            
            $action = '<a href="'. route('contact.show',$data->id) .'" class="btn btn-primary" title="Show Detail"><i class="fa fa-eye"></i></a>';
            $action .='<button data-url="'.url('contact/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Contact Details" data-name="'.$data->id.'"><i class="fa fa-trash"></i></button>';
            
            return $action;

        })

        ->rawColumns(['action','user'])
        ->make(true);

     }


  
public function create($id=null){
 if($id != null){
    $data = Contactus::where('id',$id)->first();
    if($data)
    {
        return view('admin.contact.create',compact('data'));
    }
    else {
        $notification = array(
            'message' => 'Oops! Something went wrong..',
            'alert-type' => 'error'
        );
        return Redirect::route('admin.contact.create')->with($notification);
    }  
 }
     
 return view('admin.contact.create');

}

public function store(Request $request,$id = null){
   
      $data = Contactus::updateOrCreate(['id' => $request->id],[
        'first_name'      => $request->first_name,
        'last_name'      => $request->last_name,
        'slug'      => Helper::createSlug($request->name),
        'color_code'=> $request->color_code, 
        'status'    => $request->status, 
      ] );
      
      $notification = [
        'message' => !empty($id) ? "Contact updated successfully." : "Contact Created Successfully",
        'alert-type' => 'success',
    ];

    return Redirect::route('contact.index')->with($notification);

}


public function show($id){
    $data = Contactus::where('id',$id)->first();
    return view('admin.contact.create',compact('data'));
   
}


public function destroy($id) {
    
    try {
        Contactus::where('id', $id)->delete();
        $notification = [
          'message'    => "Contact Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
       'message'    => "something went Wrong",
       'alert-type' => 'error',
      ];
    }
    return Redirect::route('contact.index')->with($notification);

}
}
