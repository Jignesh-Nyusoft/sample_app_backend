<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SubCategoryController extends Controller
{
    public function index($id = null){

        return view("admin.subcategory.list",compact("id"));

    }

     public function Datatable(Request $request){
        $data = Category::where('parent_id',$request->id)->orderBy('id','DESC');
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

        ->addColumn('category_image', function ($data) use ($request) {
            $imageUrl = '<img class="user_img" src="'.asset($data->image).'">';
            return $imageUrl;
        })

        ->addColumn('action', function ($data) {
            $action = '<a href="'. route('subcategories.edit',$data->id) .'" class="btn btn-primary" title="Edit Subcategory"><i class="fa fa-edit"></i></a>';
            $action .= '<button data-url="'.url('subcategory/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete SubCategory" data-name="'.$data->name.'"><i class="fa fa-trash"></i></button>';
            return $action;
        })
        ->rawColumns(['action','status','category_image'])
        ->make(true);
    }


   public function edit($id){
    $data = Category::where('id',$id)->first();
    $category = Category::where('id','!=',$id)
    ->where('parent_id',null)
    ->get();
    return view('admin.subcategory.create',['data' => $data,'category' => $category]);
}


   public function create($id=null){

    $category = Category::where('id','!=',$id)->get();
    if($id != null){
       $data = Category::where('id',$id)->first();
       
       if($data)
       {
           return view('admin.category.create',compact('data','category'));
       }
       else{
           $notification = array(
           'message'    => 'Oops! Something went wrong..',
           'alert-type' => 'error'
           );
           return Redirect::route('admin.category.create')->with($notification);
    }  
    }
     
    return view('admin.category.create',compact('category'));

 }


    public function store(CategoryRequest $request,$id = null){
             
        $data = Category::find($request->id);

        if($request->file('image')){
            $image = Helper::UploadImage($request->file('image'),'category');
        }elseif(!empty($data) && $data->image != ''){
            $image = $data->image;
        }else{
            $image = null;
        }

          $data = Category::updateOrCreate(['id' => $request->id ?? ''],[
            
            'name'      => $request->name,
            'parent_id' => $request->parent_id,
            'slug'      => Helper::createSlug($request->name),
            'image'     => $image, 
            'status'    => $request->status, 

          ]);
    

        $notification = [

            'message' => !empty($id) ? "SubCategory updated Successfully." : "SubCategory Created Successfully",
            'alert-type' => 'success',
        
        ];

        $id = $request->parent_id;
        return view("admin.subcategory.list",compact("id"));
    
    
    }

public function destroy($id) {
    
    try {
        Category::where('id', $id)->delete();
        $notification = [
          'message' => "SubCategory Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
          'message' => "something went Wrong",
          'alert-type' => 'error',
      ];
    }

    return Redirect::route('categories.index')->with($notification);

}

function subcatcreate($id = null){
    $data = [];
    $category = Category::where('id',$id)->first();
    $edit = false;
    return view('admin.category.subcreate',compact('data','category','edit'));
}


}
