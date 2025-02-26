<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faqs;
use App\Models\User;
use Validator;
use Storage;
use Redirect;
use Str;
use View;
use Mail;
use Yajra\DataTables\DataTables;


class Faqcontroller extends Controller
{
    
    public function index(Request $request)
    {
             return view('admin.faq.list');

    }

    function Datatable(Request $request,$id= null){
      
      $data = Faqs::orderBy('id','DESC');
      return DataTables::of($data)
  
      ->addColumn('action', function ($data) {
          
          $action = '<a href="'. route('admin.faqs.edit',['id'=>$data->id]) .'" class="btn btn-primary" title="Edit User"><i class="fa fa-edit"></i></a>';

          if ($data->deleted_at == null){
              $action .='<button data-url="'.route('admin.faqs.delete',$data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete User" data-name="'.$data->name.'"><i class="fa fa-trash"></i></button>';
          }else{
              $action .='<button data-url="'.route('admin.color.restore',$data->id).'" data-toggle="modal" data-target="#restoreModal" class="btn btn-success" title="Restore User" data-name="'.$data->name.'"><i class="icon-reload"></i></button>';
          };
          
          return $action;

      })
      ->rawColumns(['action'])
      ->make(true);


    }
public function data(){
  
  $data = Faqs::get();

  return DataTables::of($data)

  ->editColumn('answer', function($data) {
    return 'Hi ' . $data->answer . '!';
})

  ->addColumn('action', function ($data) {
      
    
      $action = '<button data-url="#"  class="btn btn-info" title="View"><i class="fa fa-eye"></i>';       
      $action .= '<a href="'. route('admin.faq.edit',['id'=>$data->id]) .'" class="btn btn-primary" title="Edit User"><i class="fa fa-edit"></i></a>';
      $action .='<button data-url="'.route('admin.faq.delete',$data->id).'" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete User" data-name="'.$data->name.'"><i class="fa fa-trash"></i></button>';
      
      return $action;

  })
  ->rawColumns(['action'])
  ->make(true);

}


    public function create($id=null)
    {
      if($id!=null)
      { 
        $data = Faqs::select('*')->where('id',$id)->first();
        if($data)
        {
          return view('admin.faq.create',compact('data'));
        }
        else {
          $notification = array(
              'message' => 'Oops! Something went wrong.',
              'alert-type' => 'error'
          );
          return Redirect::route('admin.faqs')->with($notification); 
        }

      }

      return view('admin.faq.create');
    }

    public function store(Request $request, $id=null)
    {
        $input=$request->all();
        $rules['question'] 	= "required";
        $rules['answer'] 	="required";
        
        $errorMsg		= "Oops ! Please fill the required fields.";
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return response()->json(['errorArray'=>$validator->errors(),'error_msg'=>$errorMsg,'slideToTop'=>'yes']);
        }
        else {
          Faqs::updateOrCreate(['id'=>$id],$input);

          $output['status']		    = 'success';

          if($id!=null)
          $output['msg']			    = "FAQs updated successfully.";
          else
          $output['msg']			    = "FAQs created successfully.";
          $output['msgHead']	    = "Success! ";
          $output['msgType']	    = "success";
          $output['slideToTop']	  =  true;

          $notification = array(
            'message' => $output['msg'],
            'alert-type' => $output['status'],
          );
          return Redirect::route('admin.faqs')->with($notification);
        }
    } 

    public function delete($id)
    {
        try {
            $detail = Faqs::select('*')->where('id',$id)->firstOrFail();
            $detail->delete();
            $notification = array(
                'message' => 'Success! FAQs deleted successfully.',
                'alert-type' => 'success'
            );
        } catch (\Faqs $e) {
            $notification = array(
                  'message' => 'Oops! Something went wrong.',
                  'alert-type' => 'error'
              );
            return Redirect::route('admin.faqs')->with($notification);
        }
        return Redirect::route('admin.faqs')->with($notification);
    }
}
