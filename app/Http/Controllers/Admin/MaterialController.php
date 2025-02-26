<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function index(){
       
        return view("admin.material.list");

    }


     public function Datatable(Request $request){

        $data = Material::orderBy('id','DESC');
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
            
            $action = '<a href="'. route('material.edit',$data->id) .'" class="btn btn-primary" title="Edit material"><i class="fa fa-edit"></i></a>';
            $action .='<button data-url="'.url('material/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Material" data-name="'.$data->name.'"><i class="fa fa-trash"></i></button>';

            return $action;

        })
        ->rawColumns(['action','status'])
        ->make(true);
     }

     
public function create($id=null){
 
    if($id != null){
    $data = Material::where('id',$id)->first();
    if($data)
    {
        return view('admin.material.create',compact('data'));
    }
    else{
        $notification = array(
            'message'    => 'Oops! Something went wrong..',
            'alert-type' => 'error'
        );
        return Redirect::route('admin.material.create')->with($notification);
    }  
    }
     
        return view('admin.material.create');

}


public function store(Request $request,$id = null){

    $validator = Validator::make($request->all(),[
        'name'        => 'required|string|min:1|max:190',
        'status'      => 'required|string|min:1|max:190',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator);
    }
   
      $data = Material::updateOrCreate(['id' => $request->id],[
        'name'      => $request->name,
        'slug'      => Helper::createSlug($request->name), 
        'status'    => $request->status,
      ]);
      
      $notification = [

        'message' => !empty($id) ? "Material updated Successfully." : "Material Created Successfully",
        'alert-type' => 'success',
      
      ];

return Redirect::route('material.index')->with($notification);

}

  public function edit($id){
    
      $data = Material::where('id',$id)->first();
      return view('admin.material.create',compact('data'));
     
  }


public function destroy($id) {
    
    try {
        Material::where('id', $id)->delete();
        $notification = [
          'message' => "Material Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
      'message' => "something went Wrong",
       'alert-type' => 'error',
      ];
    }
    return Redirect::route('material.index')->with($notification);

}

}
