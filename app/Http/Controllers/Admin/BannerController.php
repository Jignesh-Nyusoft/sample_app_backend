<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use DB;
use Illuminate\Http\Request;
use Redirect;
use Validator;
use Yajra\DataTables\DataTables;

class BannerController extends Controller
{
    public function index(){
       
        return view("admin.banner.list");

    }

     public function Datatable(Request $request){

        $data = DB::table('banners')->orderBy('id','DESC')->get();

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
            $action = '<a href="'. route('banners.edit',$data->id) .'" class="btn btn-primary" title="Edit Banner"><i class="fa fa-edit"></i></a>';
            $action .= '<button data-url="'.url('banners/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Banner" data-name=""><i class="fa fa-trash"></i></button>';
            return $action;
        })

        ->rawColumns(['action','status','brandimage'])
        ->make(true);
     }


   public function edit($id){

    $data = Banner::where('id',$id)->first();

    return view('admin.banner.create',compact('data'));
   
   }

   public function create($id=null){
   
    if($id != null){
       $data = Banner::where('id',$id)->first();
       if($data)
       {
           return view('admin.banner.create',compact('data'));
       }
       else{
           $notification = array(
           'message' => 'Oops! Something went wrong..',
           'alert-type' => 'error'
           );
           return Redirect::route('admin.banner.create')->with($notification);
       }  
    }
     
     return view('admin.banner.create');

    }


    public function store(Request $request,$id = null){

        
        $validator = Validator::make($request->all(),[
            'name'        => 'required|string|min:1|max:190',
            'status'      => 'required',   
        ]);

       if($id == null){
        $validator = Validator::make($request->all(),[
           'image'       => 'required|mimes:png,jpg,jpeg',
        ]);
       }

        
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
             
        $data = Banner::find($request->id);

        if($request->file('image')){
            $image = Helper::UploadImage($request->file('image'),'banner');
        }elseif(!empty($data) && $data->image != ''){
            $image = $data->image;
        }else{
            $image = null;
        }

          $data = Banner::updateOrCreate(['id' => $request->id ?? ''],[
            'name'      => $request->name,
            'slug'      => Helper::createSlug($request->name),
            'image'     => $image, 
            'status'    => $request->status, 
          ] );
    
          $notification = [

            'message'    => !empty($id) ? "Banner updated Successfully." : "Banner Created Successfully",
            'alert-type' => 'success',
      
        ];
        
        return Redirect::route('banners.index')->with($notification);
    
    
    }

public function destroy($id) {
    
    try {
        Banner::where('id', $id)->delete();
        $notification = [
          'message'    => "Banner Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
          'message'    => "something went Wrong",
          'alert-type' => 'error',
      ];
    }

    return Redirect::route('banners.index')->with($notification);

}
}
