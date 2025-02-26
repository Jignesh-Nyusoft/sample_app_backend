<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BrandController extends Controller
{
    
    public function index(){
       
        return view("admin.brand.list");

    }

     public function Datatable(Request $request){

        $data = DB::table('brands')->orderBy('id','DESC')->get();

        return DataTables::of($data)

        ->addColumn('status', function ($data) use ($request) {            
            $status = '<span class="badge badge-danger">Inactive</span>';
            if($data->status == 'active'){
                $status = '<span class="badge badge-success">Active</span>';
            }
            return $status;
        })

        ->addColumn('brandimage', function ($data) use ($request) {
            $imageUrl = '<img style="height: 65px; width: 70px;" src="'.asset($data->image).'">';
            
            return $imageUrl ?? null;
        })

        ->addColumn('action', function ($data) {
            $action = '<a href="'. route('brands.edit',$data->id) .'" class="btn btn-primary" title="Edit Brand"><i class="fa fa-edit"></i></a>';
            $action .= '<button data-url="'.url('brands/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Brand" data-name=""><i class="fa fa-trash"></i></button>';
            return $action;
        })
        ->rawColumns(['action','status','brandimage'])
        
        ->make(true);
     }


   public function edit($id){
    $data = Brand::where('id',$id)->first();
    return view('admin.brand.create',compact('data'));
   }


  
   public function create($id=null){
    if($id != null){
       $data = Brand::where('id',$id)->first();
       if($data)
       {
           return view('admin.brand.create',compact('data'));
       }
       else{

           $notification = array(
           'message' => 'Oops! Something went wrong..',
           'alert-type' => 'error'
           );
           return Redirect::route('admin.brand.create')->with($notification);
       
        }  
    }
     
     return view('admin.brand.create');

    }


    public function store(Request $request,$id = null){
        $validator = Validator::make($request->all(),[
            'name'        => 'required|string|min:1|max:190|unique:brands,name,'.$request->id,
            'image'       => 'nullable|mimes:png,jpg,jpeg',
            'status'      => 'required|string|min:1|max:190',   
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
             
        $data = Brand::find($request->id);

        if($request->file('image')){
            $image = Helper::UploadImage($request->file('image'),'brand');
        }elseif(!empty($data) && $data->image != ''){
            $image = $data->image;
        }else{
            $image = null;
        }

          $data = Brand::updateOrCreate(['id' => $request->id ?? ''],[
            
            'name'      => $request->name,
            'slug'      => Helper::createSlug($request->name),
            'image'     => $image, 
            'status'    => $request->status, 

          ]);
    
          $notification = [

            'message' => !empty($id) ? "Brand updated Successfully." : "Brand Created Successfully",
            'alert-type' => 'success',
        
        ];

        return Redirect::route('brands.index')->with($notification);
    
    
    }

public function destroy($id) {
    
    try {
        Brand::where('id', $id)->delete();
        $notification = [
          'message' => "Brand Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
          'message' => "something went Wrong",
          'alert-type' => 'error',
      ];
    }
    return Redirect::route('brands.index')->with($notification);

}




}
