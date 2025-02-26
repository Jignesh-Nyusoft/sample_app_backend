<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{

    public function index(){
       
        return view("admin.size.list");

    }


     public function Datatable(Request $request){

        $data = Size::orderBy('id','DESC');
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
            
            $action  = '<a href="'. route('size.edit',$data->id) .'" class="btn btn-primary" title="Edit Size"><i class="fa fa-edit"></i></a>';
            $action .='<button data-url="'.url('size/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Size" data-name="'.$data->name.'"><i class="fa fa-trash"></i></button>';
    
            return $action;

        })
        ->rawColumns(['action','status'])
        ->make(true);
        }
  
     public function create($id=null){
      if($id != null){
         $data = Size::where('id',$id)->first();
         if($data)
         {
             return view('admin.size.create',compact('data'));
         }
         else {
             $notification = array(
                 'message' => 'Oops! Something went wrong..',
                 'alert-type' => 'error'
             );
             return Redirect::route('admin.size.create')->with($notification);
         }  
      }
          
      return view('admin.size.create');
     
     }


    public function store(Request $request,$id = null){
    
        $validator = Validator::make($request->all(),[
           
            'name'        => 'required|string|min:1|max:190|unique:sizes,name,'.$request->id,
            'status'      => 'required|string|min:1|max:190',
    
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
       
          $data = Size::updateOrCreate(['id' => $request->id],[
            'name'      => $request->name,
            'slug'      => Helper::createSlug($request->name), 
            'status'    => $request->status, 
          ]);
          
          $notification = [
            'message' => !empty($id) ? "Size updated successfully." : "Size Created Successfully",
            'alert-type' => 'success',
        ];
    
        return Redirect::route('size.index')->with($notification);
    
    }

    public function edit($id){
        $data = Size::where('id',$id)->first();
        return view('admin.size.create',compact('data'));
       
    }

    public function destroy($id) {
        
        try {
            Size::where('id', $id)->delete();
            $notification = [
              'message'    => "Size Deleted Successfully",
              'alert-type' => 'success',
          ];    
        } catch (\Throwable $th) {
            $notification = [
           'message'    => "something went Wrong",
           'alert-type' => 'error',
          ];
        }
        return Redirect::route('size.index')->with($notification);
    
    }



}
