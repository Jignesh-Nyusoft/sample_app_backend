<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function index(){
       
        return view("admin.product.list");

    }

    public function ApproveIndex(){
        
    return view("admin.product.approve");

    }

    
     
    public function Datatable(Request $request){

        $data = Product::where('is_approved',true)->orderBy('id','DESC');
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
        
        ->addColumn('category', function ($data)  {            
             $category = Category::find($data->category_id);
            return $category->name ?? 'No Data';
        })

        ->addColumn('listed_by', function ($data)  {            
            $user = User::find($data->user_id);
           return $user->first_name ?? 'No Data';
        })

        ->addColumn('image', function ($data) use ($request) {
            $imageUrl = '<img style ="height: 65px; width: 70px;" src="'.asset($data->image).'">';
            return $imageUrl;
        })

        ->addColumn('action', function ($data) {
        
            $action = '<a href="'. route('product.show',$data->id) .'" class="btn btn-primary" title="Show Product"><i class="fa fa-eye"></i></a>';
            return $action;

        })
        ->rawColumns(['category','action','status','listed_by','image'])
        ->make(true);

     }

public function ApproveDatatable(Request $request){
    
    $data = Product::where('is_approved',false)->orderBy('id','DESC');
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

        ->addColumn('category', function ($data)  {            
             $category = Category::find($data->category_id);
            return $category->name ?? 'No Data';
        })

        ->addColumn('listed_by', function ($data)  {            
            $user = User::find($data->user_id);
            return $user->first_name ?? 'No Data';
        })

        ->addColumn('image', function ($data) use ($request) {
            $imageUrl = '<img style ="height: 65px; width: 70px;" src="'.asset($data->image).'">';
            return $imageUrl;
        })

        ->addColumn('action', function ($data) {
            $action  = '<a href="'.  route('product.show',$data->id) .'" class="btn btn-primary" title="Show Product"><i class="fa fa-eye"></i></a>';
            $action .= '<a href="'. url('request-approve',$data->id) .'" class="btn btn-success" title="Approved Product">Approve<i class=""></i></a>';
            return $action;

        })
        ->rawColumns(['category','action','status','listed_by','image'])
        ->make(true);

}


     public function show($id){
       
        $data = Product::with('User','Brand','Size','Condition','Material','Color','Suitable')->find($id);
        return view('admin.product.show',compact('data'));
        
     }

    public function ApproveRequest($id){
        
     $data = Product::where('id',$id)->first();
     $data-> update([
        'is_approved' => true,
        'status'      => 'active'
     ]);

     $seller = User::find($data->user_id);
     $notification = [
        'title'    => "Product Approved",
        'body'     => "Product Approved Sucessfully",
        'message'  => "Hello $seller->first_name, your product $data->product_name has been successfully approved by Admin ,Please Have a look",
        'sender_id'=> 1,
        'image_url'=> $data?->product_image, 
        'type'     => "Order_Checkout",
        'user_id'  => $seller->id, 
        'order_id' => null, 
        
       ];

       if($seller->device_token != null){
        NotificationHelper::sendNotifications($seller->device_token,$notification,$data = []);
       }
       $seller->notify(new OrderNotification($notification));
  
     $notification = [
        'message'    => "Product Approved Successfully",
        'alert-type' => 'success',
    ];

    return redirect('product-index-view')->with($notification);
    
    }


     function StatusUpdate(Request $request) {
    
 
      $data = Product::where('id',$request->id)->first();
    
      if($data->status == 'active'){
           
        $status = 'inactive';
        
      }elseif($data->status == 'inactive'){
        
        $status = 'active';
        
      }
    
      $data->update(['status'=> $status]);
    
    return response()->json(['status'=> 200]);
    
    }

}
