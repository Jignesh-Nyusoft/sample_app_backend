<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use DB;
use Illuminate\Http\Request;
use Redirect;
use Validator;
use Yajra\DataTables\DataTables;

class CouponController extends Controller
{
    public function index(){
       
        return view("admin.coupon.list");

    }

     public function Datatable(Request $request){

        $data = DB::table('coupons')->orderBy('id','DESC')->get();

        return DataTables::of($data)
        ->addColumn('status', function ($data) use ($request) {            
            $status = '<span class="badge badge-danger">Inactive</span>';
            if($data->status == 'active'){
                $status = '<span class="badge badge-success">Active</span>';
            }
            return $status;
        })

         ->addColumn('action', function ($data) {
            $action = '<a href="'. route('coupons.edit',$data->id) .'" class="btn btn-primary" title="Edit Coupon"><i class="fa fa-edit"></i></a>';
            $action .= '<button data-url="'.url('coupons/delete', $data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete Coupon" data-name=""><i class="fa fa-trash"></i></button>';
            return $action;

        })
        ->rawColumns(['action','status'])
        
        ->make(true);

    }


   public function edit($id){

    $data = Coupon::where('id',$id)->first();

    return view('admin.coupon.create',compact('data'));
   
   }


  
   public function create($id=null){
   
    if($id != null){
       $data = Coupon::where('id',$id)->first();
       if($data)
       {
           return view('admin.coupon.create',compact('data'));
       }
       else{
           $notification = array(
           'message'    => 'Oops! Something went wrong..',
           'alert-type' => 'error'
           );
           return Redirect::route('admin.coupon.create')->with($notification);
       }  
       }
     
     return view('admin.coupon.create');

    }


    public function store(Request $request,$id = null){
       
       $request->validate([

        'name'        => 'required|string|min:1|max:190',
        'coupon_code' => 'required|unique:coupons,coupon_code,'. $request->id ,
        'min_amount'  => 'required',
        'no_of_coupon'=> 'required',
        'valid_from'  => 'required',
        'valid_till'  => 'required',
        'status'      => 'required|string|min:1|max:190',

        ]);

                     
        $data = Coupon::updateOrCreate(['id' => $request->id ?? ''],[

            'name'       => $request->name,
            'slug'       => Helper::createSlug($request->name),
            'coupon_code'=> $request->coupon_code, 
            'valid_till' => $request->valid_till, 
            'valid_from' => $request->valid_from,
            'coupon_type'=> 'flat', 
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount ?? 0,
            'discount_amount' => $request->discount_amount,
            'status'       => $request->status, 
            'no_of_coupon' => $request->no_of_coupon
            
        ]);
    
        $notification = [

            'message' => !empty($id) ? "Coupon updated Successfully." : "Coupon Created Successfully",
            'alert-type' => 'success',
        ];
        
        return Redirect::route('coupons.index')->with($notification);
    
    
    }

public function destroy($id) {
    
    try {
        Coupon::where('id', $id)->delete();
        $notification = [
          'message'    => "Coupon Deleted Successfully",
          'alert-type' => 'success',
      ];    
    } catch (\Throwable $th) {
        $notification = [
          'message'    => "something went Wrong",
          'alert-type' => 'error',
      ];
    }

    return Redirect::route('coupons.index')->with($notification);

}
}
