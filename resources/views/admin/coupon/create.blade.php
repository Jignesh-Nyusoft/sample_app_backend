@extends('admin.layouts.default')
@section('content')

<style>
   #imgOut {
    display: none; /* Start hidden */
    max-width: 100%;
    height: auto;
  }
</style>

<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
         
          <div class="x_content">

            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
               <form id="user-form" class="form-horizontal" method="post"  action="@if(isset($data)){{route('coupons.store',$data->id)}}@else{{route('coupons.store')}}@endif" enctype="multipart/form-data">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                  @if (isset($data) && !empty($data))
                  <input type="hidden" name="id" value="{{$data->id}}"/>
                  @endif
                  <fieldset>
                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">
                            <div class="x_title">
                                 <h2>@if(isset($data)){{'Edit Coupon'}}@else{{'Add Coupon'}}@endif</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                 <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                              <div class="clearfix"></div>
                              </div>
                                <div class="x_content">
                                    <br/>
                                    @if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align ">Coupon Name<span class="red">*</span> </label>
                                        <div class="col-md-6 col-sm-6  ">
                                          <input class="form-control" 
                                          type="text" 
                                          value="{{ isset($data->name) ? $data->name : old('name') }}" 
                                          placeholder="Please Enter Name" 
                                          name="name">
                                            @if($errors->has('name'))
                                            <div class="error" style="color: red">{{ $errors->first('name') }}</div>
                                            @endif
                                          </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align ">Coupon Code<span class="red">*</span> </label>
                                        <div class="col-md-6 col-sm-6  ">
                                        <input class="form-control" type="text" value="{{ isset($data->coupon_code) ? $data->coupon_code : old('coupon_code') }}" placeholder="Please Enter Coupon Code" name="coupon_code">
                                        @if($errors->has('coupon_code'))
                                        <div class="error" style="color: red">{{ $errors->first('coupon_code') }}</div>
                                        @endif
                                       </div>
                                    </div>




                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Minimum Amount<span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6  ">
                                           <input class="form-control" type="text" value="{{ isset($data->min_amount) ? $data->min_amount : old('min_amount') }}" placeholder="Please Enter Minimum amount" name="min_amount">
                                           @if($errors->has('min_amount'))
                                           <div class="error" style="color: red">{{ $errors->first('min_amount') }}</div>
                                           @endif
                                          </div>
                                   </div>


                                   {{-- <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Maximum Amount<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                     <input class="form-control" type="text" value="{{ isset($data->max_amount) ? $data->max_amount : old('max_amount') }}" placeholder="Please Enter Maximum amount" name="max_amount">
                                     @if($errors->has('max_amount'))
                                     <div class="error" style="color: red">{{ $errors->first('max_amount') }}</div>
                                     @endif
                                    </div>
                                    </div> --}}

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Discount Amount<span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6  ">
                                        <input class="form-control" type="text" value="{{ isset($data->discount_amount) ? $data->discount_amount : old('discount_amount') }}" placeholder="Please Enter Discount amount" name="discount_amount">
                                        @if($errors->has('discount_amount'))
                                        <div class="error" style="color: red">{{ $errors->first('discount_amount') }}</div>
                                        @endif
                                       </div>
                                       </div>


                                       <div class="item form-group">
                                          <label class="col-form-label col-md-3 col-sm-3 label-align">Valid From <span class="red">*</span></label>
                                          <div class="col-md-6 col-sm-6">
                                              <input class="form-control" type="date" 
                                                     value="{{ isset($data->valid_from) ? $data->valid_from : old('valid_from') }}" 
                                                     placeholder="Please Enter valid from" 
                                                     name="valid_from" 
                                                     min="{{ \Carbon\Carbon::today()->toDateString() }}">
                                              @if($errors->has('valid_from'))
                                                  <div class="error" style="color: red">{{ $errors->first('valid_from') }}</div>
                                              @endif
                                          </div>
                                      </div>
   


                                      <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align">Valid Till <span class="red">*</span></label>
                                       <div class="col-md-6 col-sm-6">
                                           <input class="form-control" type="date" 
                                                  value="{{ isset($data->valid_till) ? $data->valid_till : old('valid_till') }}" 
                                                  placeholder="Please Enter valid from" 
                                                  name="valid_till" 
                                                  min="{{ \Carbon\Carbon::today()->toDateString() }}">
                                           @if($errors->has('valid_till'))
                                               <div class="error" style="color: red">{{ $errors->first('valid_till') }}</div>
                                           @endif
                                       </div>
                                   </div>


                                   <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Number of Coupons<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                     <input class="form-control" type="text" value="{{ isset($data->no_of_coupon) ? $data->no_of_coupon : old('no_of_coupon') }}" placeholder="Please Enter Number of coupons" name="no_of_coupon">
                                     @if($errors->has('no_of_coupon'))
                                     <div class="error" style="color: red">{{ $errors->first('no_of_coupon') }}</div>
                                     @endif
                                    </div>
                                    </div>

                                 


                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Status<span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6">
                                          <select name="status" class="form-control form-control-m">
                                             <option value="active" {{ isset($data) && $data->status == 'active' ? 'selected' : '' }}>Active</option>
                                             <option value="inactive" {{ isset($data) && $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                         </select>
                                         @if($errors->has('status'))
                                         <div class="error" style="color: red">{{ $errors->first('status') }}</div>
                                         @endif
                                       </div>
                                    </div>

                                    <img id="img-preview" src="@if(isset($data) && !empty($data->profile_image)) {{url($data->profile_image ?? '')}}@endif"  style="margin-top: -13%; margin-left: 76%; height: 92px;"/> 
                                </div>
                           </div>
                        </div>
                     </div>

                  </fieldset>                    

                     <div class="ln_solid"></div>

                     <fieldset>
                        <div class="form-group row">
                              <div class="col-md-7 col-sm-7 offset-md-5">
                                 <a href="{{route('admin.dashboard')}}" class="btn btn-warning">Discard</a>
                                 <button class="btn btn-success submit" type="submit">Save</button>
                              </div>
                        </div>
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

<script>

   function readURL(input) {
     console.log(input.files);
     if (input.files && input.files[0]) {
       var reader = new FileReader();
       reader.onload = function (e) {
         $("#img-preview").attr("src", e.target.result);
       };
       reader.readAsDataURL(input.files[0]);
     } else {
       $("#img-preview").attr("src", ""); 
       $("#img-preview").attr("alt", "No image selected");
     }
   }
   
   </script>

<script type="text/javascript">
    $(function() {
 
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