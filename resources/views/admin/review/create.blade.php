@extends('admin.layouts.default')
@section('content')
<style>
   input{
  display: block;
  width: 50%;
  float: left;
  height: 47px;
}
input[type="text"]:invalid{
  outline: 2px solid red;
}

/* body {
  
  padding: 3em;
  display: flex;
  min-height: 100vh;
  justify-content: center;
  align-items: center;
} */
</style>

<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
         
          <div class="x_content">

            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
               <form id="user-form" class="form-horizontal" method="post"  action="@if(!empty($data) && isset($data)){{route('color.store',$data->id)}}@else{{route('color.store')}}@endif" enctype="multipart/form-data">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                  <input type="hidden" name="id" value="{{ $data->id  ?? ''}}" />
                  <fieldset>
                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">
                            <div class="x_title">
                                 <h2>@if(isset($data)){{'Show Review'}}@else{{'Show Review'}}@endif</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                 <li><a class="collapse-link"><i cla ss="fa fa-chevron-up"></i></a> </li>
                                </ul>
                                <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                <br/>

                        

                                    <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Customer Name<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                    <input readonly class="form-control" type="text" value="@if(isset($data->ReviewGivenByUser->first_name)){{$data->ReviewGivenByUser->first_name}}@endif" placeholder="Color Name" name="color_name">
                  
                                   </div>
                                   </div>

                                   <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Order Number<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                    <input readonly class="form-control" type="text" value="@if(isset($data->order->order_number)){{$data->order->order_number}}@endif" placeholder="Color Name" name="color_name">
                              
                                </div>
                                </div>

                                    <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Seller Name<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                    <input readonly class="form-control" type="text" value="@if(isset($data->seller->first_name)){{$data->seller->first_name}}@endif" placeholder="Color Name" name="color_name">
                                    @if($errors->has('color_name'))
                                    <div class="error" style="color: red">{{ $errors->first('color_name') }}</div>
                                    @endif
                                   </div>
                                   </div>

                                    <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Product Name<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                    <input readonly class="form-control" type="text" value="@if(isset($data->product->product_name)){{$data->product->product_name}}@endif" placeholder="Color Name" name="color_name">
                                    @if($errors->has('color_name'))
                                    <div class="error" style="color: red">{{ $errors->first('color_name') }}</div>
                                    @endif
                                   </div>
                                   </div>


                                   <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Product Name<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                        <input readonly class="form-control" type="text" value="@if(isset($data->product->product_name)){{$data->product->product_name}}@endif" placeholder="Color Name" name="color_name">
                                        @if($errors->has('color_name'))
                                        <div class="error" style="color: red">{{ $errors->first('color_name') }}</div>
                                        @endif
                                </div>
                                </div>

                                   <div class="item form-group">
                                   <label class="col-form-label col-md-3 col-sm-3 label-align">Review<span class="red">*</span> </label>
                                   <div class="col col ">
                                   <textarea disabled name="" id="" cols="30" rows="10">{{$data->review ?? 'No Data'}} </textarea>
                                   @if($errors->has('color_name'))
                                   <div class="error" style="color: red">{{ $errors->first('color_name') }}</div>
                                   @endif
                                </div>
                                </div>

                                
                                <div class="row">
                                 <div class="col">
                                     <label for="">Review Images</label> <br>
                                    @if($data->ReviewImages)
                                    @foreach ($data->ReviewImages as $images)
                                    <img style="width: 146px; border: double;height: 99px;" src="{{asset($images->image)}}" alt="">  
                                    @endforeach
                                    @endif
                                  
                             </div>
                             </div>
                                
                        </div>
                        </div>
                        </div>
                        </div>
                  </fieldset>                    
                     <div class="ln_solid"></div>
                     <fieldset>
                        {{-- <div class="form-group row">
                              <div class="col-md-7 col-sm-7 offset-md-5">
                                 <a href="{{route('color.index')}}" class="btn btn-warning">Discard</a>
                                 <button class="btn btn-success submit" type="submit">Save</button>
                              </div>
                        </div> --}}
                     </fieldset>

               </form>
            </div>

            </div>
          </div>
        </div>
    </div>
</div>


@stop
@section('footer_scripts')

<script src="{{asset('admin_assets/js/jquery.validate.min.js')}}"></script>

<script type="text/javascript">
    $(function() {
      $('#colorpicker').on('input', function() {
	$('#hexcolor').val(this.value);
});
$('#hexcolor').on('input', function() {
  $('#colorpicker').val(this.value);
});
       jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-z]+$/i.test(value);
     }, "Letters only please"); 
 
       $("#users-form").validate({
          rules: {
             first_name: {
                required: true,
                lettersonly: true
             },
             last_name: {
                required: true,
                lettersonly: true
             },
             email: {
                required: true,
                email: true
             },
          },
          messages: {
             first_name: {
                required: "Please enter a first name.",
                lettersonly: "Please enter only alphabets."
             },
             last_name: {
                required: "Please enter a last name.",
                lettersonly: "Please enter only alphabets."
             },
             
             email: {
                required: "Please enter a email.",
                lettersonly: "Please enter a valid email."
             },
          },
          submitHandler: function(form) {
             form.submit();
          }
       });
    });
 </script>
@stop