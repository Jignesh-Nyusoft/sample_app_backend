<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Redirect;
use Yajra\DataTables\DataTables;

class ReviewController extends Controller
{
    public function index(){
       
        return view("admin.review.list");

    }

     public function Datatable(Request $request){

        $data = OrderReview::orderBy('id','DESC');
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
            
             return $name;
        })
        ->addColumn('seller', function ($data) use ($request){      

            if($data->seller_id != null){
                $user = User::find($data->seller_id); 
                $name = $user->first_name;     
            }else{
                $name = 'No Data';
            } 
            
             return $name;
        })


        ->addColumn('order', function ($data) use ($request){      

            if($data->order_id != null){
                $user = Order::find($data->order_id); 
                $name = $user->order_number;     
            }else{
                $name = 'No Data';
            } 
            
             return $name;
        })

        ->addColumn('product', function ($data) use ($request){      

            if($data->user_id != null){
                $user = Product::find($data->product_id); 
                $name = $user->product_name;     
            }else{
                $name = 'No Data';
            } 
            
             return $name;
        })


        ->addColumn('action', function ($data) {
            
            $action  = '<a href="'. route('review.show',$data->id) .'" class="btn btn-primary" title="Show Review"><i class="fa fa-eye"></i></a>';
            $action .='<button data-url="'.url('review/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Contact Details" data-name="'.$data->id.'"><i class="fa fa-trash"></i></button>';
            
            return $action;

        })
        ->rawColumns(['action','user','seller','product','order'])
        ->make(true);

     }


  
public function create($id=null){
 if($id != null){
    $data = OrderReview::where('id',$id)->first();
    if($data)
    {
        return view('admin.review.create',compact('data'));
    }
    else{
        $notification = array(
            'message'    => 'Oops! Something went wrong..',
            'alert-type' => 'error',
        );
        return Redirect::route('admin.review.create')->with($notification);
    }  
 }
     
 return view('admin.review.create');

}


public function store(Request $request,$id = null){
   
      $data = OrderReview::updateOrCreate(['id' => $request->id],[
        'name'      => $request->color_name,
        'slug'      => Helper::createSlug($request->color_name),
        'color_code'=> $request->color_code, 
        'status'    => $request->status, 
      ]);
      
      $notification = [
        'message'    => !empty($id) ? "Review updated successfully." : "Review Created Successfully",
        'alert-type' => 'success',
    
     ];

    return Redirect::route('review.index')->with($notification);

}



public function show($id){

    $data = OrderReview::with('ReviewImages','ReviewGivenByUser','seller','Product')->where('id',$id)->first();
    return view('admin.review.create',compact('data'));
   
}


public function destroy($id) {
    
    try {
        OrderReview::where('id', $id)->delete();
        $notification = [
          'message' => "Review Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
      'message' => "something went Wrong",
      'alert-type' => 'error',

      ];
    }
    return Redirect::route('review.index')->with($notification);

}
}
