<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Suitable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SuitableController extends Controller
{
    public function index(){
       
        return view("admin.suitable.list");

    }

     public function Datatable(Request $request){

        $data = Suitable::orderBy('id','DESC');
        return \Yajra\DataTables\DataTables::of($data)
        ->filter(function ($query) use ($request, $data) {
            if ($request->has('slug') && !empty($request->slug)) {
                $query->where(function($q) use ($request, $data) {
                    $q->where('slug', 'like', "%{$request->get('slug')}%");
                });
            }
        })
        
        ->addColumn('status', function ($data) use ($request) {            
            $status = '<span class="badge badge-danger">Inactive</span>';
            if($data->status == 'active'){
                $status = '<span class="badge badge-success">Active</span>';
            }
            return $status;
        })

        ->addColumn('action', function ($data) {
         $action = '<a href="'. route('suitable.edit',$data->id) .'" class="btn btn-primary" title="Edit material"><i class="fa fa-edit"></i></a>';
         $action .= '<button data-url="'.url('suitable/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Material" data-name="'.$data->name.'"><i class="fa fa-trash"></i></button>';
            
         return $action;

        })

        ->rawColumns(['action','status'])
        ->make(true);

    }


   public function edit($id){
    
    $data = Suitable::where('id',$id)->first();
    return view('admin.suitable.create',compact('data'));

   }


  
   public function create($id=null){
    if($id != null){
        
       $data = Suitable::where('id',$id)->first();
       
       if($data)
       {
           return view('admin.suitable.create',compact('data'));
       }
       else {
           $notification = array(
           'message' => 'Oops! Something went wrong..',
           'alert-type' => 'error'
           );

        return Redirect::route('admin.material.create')->with($notification);
       }  
    }
     
     return view('admin.suitable.create');

    }


    public function store(Request $request,$id = null){
    
        $validator = Validator::make($request->all(),[

            'name'        => 'required|string|min:1|max:190',
            'status'      => 'required|string|min:1|max:190',
    
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
       
          $data = Suitable::updateOrCreate(['id' => $request->id ?? ''],[
            'name'      => $request->name,
            'short_desc'=> $request->short_desc,
            'slug'      => Helper::createSlug($request->name), 
            'status'    => $request->status, 
          ]);
          
    
        return Redirect::route('suitable.index')->with('success',!empty($id) ? "Suitable Updated Successfully." : "Suitable Created Successfully");
    
    }

public function destroy($id) {
    
    try {
        Suitable::where('id', $id)->delete();
        $notification = [
          'message'    => "Suitable Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
       'message'    => "something went Wrong",
       'alert-type' => 'error',
      ];
    }
    
    
    return Redirect::route('suitable.index')->with($notification);

}




}
