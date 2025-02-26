<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    

    public function index($type = null){
       
        return view("admin.order.list",compact('type'));

    }

    public function ApproveIndex(){
        
    return view("admin.order.approve");

    }

    
     
    public function Datatable(Request $request,$type = null){
      
        
        $data = Order::orderBy('id','DESC');
        return \Yajra\DataTables\DataTables::of($data)

        ->filter(function ($query) use ($request, $data,$type) {
            if($type == 'recent'){
                $query->where('created_at', '>=', now()->subDay()); // Get orders in the last 24 hours
            }
            if($type != 'All' && $type != 'recent' ){
        
                $query->where('delivery_status',$type);
            }
          
            if ($request->has('slug') && !empty($request->slug)) {
                $query->where(function($q) use ($request, $data) {
                    $q->where('slug', 'like', "%{$request->get('slug')}%");
            });
            }
        })

        ->addColumn('user', function ($data) {
        
            $action =User::find($data->user_id);
            return $action->first_name;

        })

        ->addColumn('seller', function ($data) {
        
            $action = User::find($data->seller_id);
            return $action->first_name;

        })

        ->addColumn('payment', function ($data) {
            
            if($data->delivery_status == 'pending'){
            
                $action = '<h6><span class="badge badge-secondary">Pending</span></h6>';
            }elseif($data->delivery_status == 'processing'){
            
                $action = '<h6><span class="badge badge-info">Processing</span></h6>';

            }elseif($data->delivery_status == 'shipped'){
            
                $action = '<h6><span class="badge badge-primary">Shipped</span></h6>';

            }elseif ($data->delivery_status == 'delivered') {

                $action = '<h6><span class="badge badge-success">Delivered</span></h6>';
            }
            return $action;

        })
        
        ->addColumn('status', function ($data) {
            $action = '<span class="badge badge-success">Pending</span>';
            if($data->payment_status == 'unpaid'){
                $action = '<span class="badge badge-danger">Unpaid</span>';
            }
            return $action;

        })

        ->addColumn('product', function ($data) {
        
            $action = OrderItems::where('order_id',$data->id)->first();
            $product = Product::find($action->product_id);
            return $product->product_name;
        })

        ->addColumn('action', function ($data) {
        
           $action = '<a href="'. route('order.show',$data->id) .'" class="btn btn-primary" title="Show Order Details"><i class="fa fa-eye"></i></a>';
           return $action;

        })
          ->rawColumns(['action','payment'])
          ->make(true);

     }



     public function show($id){
       
        $order = Order::with('user','seller','deliveryaddress','orderShipment')->find($id);
        $data  = Product::with('User','Brand','Size','Condition','Material','Color','Suitable')->find($id);
        return view('admin.order.show',compact('data','order'));

     }






}
