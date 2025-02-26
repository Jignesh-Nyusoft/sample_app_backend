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
               <form id="user-form" class="form-horizontal" method="post"  action="@if(isset($data)){{route('admin.users.update',$data->id)}}@else{{route('admin.users.store')}}@endif" enctype="multipart/form-data">
               <input type="hidden" name="_token" value="{{ csrf_token() }}" />
               <fieldset>
                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">
                            <div class="x_title">
                                 <h2>@if(isset($data)){{'Edit User'}}@else{{'Add User'}}@endif</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                 <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                              <div class="clearfix"></div>
                              </div>
                                <div class="x_content">
                                    <br/>
      
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align ">First Name<span class="red">*</span> </label>
                                        <div class="col-md-6 col-sm-6  ">
                                            <input class="form-control" type="text" value="@if(isset($data->first_name)){{$data->first_name}}@endif{{old('first_name')}}" placeholder="Please Enter First Name" name="first_name">
                                            @if($errors->has('first_name'))
                                            <div class="error" style="color: red">{{ $errors->first('first_name') }}</div>
                                            @endif
                                          </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align ">Last Name<span class="red">*</span> </label>
                                        <div class="col-md-6 col-sm-6  ">
                                        <input class="form-control" type="text" value="@if(isset($data->last_name)){{$data->last_name}}@endif{{old('last_name')}}" placeholder="Please Enter Last Name" name="last_name">
                                        @if($errors->has('last_name'))
                                        <div class="error" style="color: red">{{ $errors->first('last_name') }}</div>
                                        @endif
                                       </div>
                                    </div>


                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Country Code<span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6  ">
                                       <input class="form-control" type="text" value="@if(isset($data->country_code)){{$data->country_code}}@endif{{old('Country_code')}}" placeholder="Please Enter Country Code" name="Country_code">
                                       
                                       @if($errors->has('Country_code'))
                                       <div class="error" style="color: red">{{ $errors->first('Country_code') }}</div>
                                       @endif
                                       </div>
                                   </div>


                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Mobile<span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6  ">
                                           <input class="form-control" type="text" value="@if(isset($data->mobile)){{$data->mobile}}@endif{{old('mobile')}}" placeholder="Please Enter Mobile" name="mobile">
                                           @if($errors->has('mobile'))
                                           <div class="error" style="color: red">{{ $errors->first('mobile') }}</div>
                                           @endif
                                          </div>
                                   </div>

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Email<span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6  ">
                                           <input class="form-control" type="text" value="@if(isset($data->email)){{$data->email}}@endif{{old('email')}}" placeholder="Please Enter Email" name="email">
                                           @if($errors->has('email'))
                                           <div class="error" style="color: red">{{ $errors->first('email') }}</div>
                                           @endif
                                          </div>
                                   </div>

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Gender<span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6">
                                          <select name="gender" class="form-control form-control-lg">
                                             <option>Male</option>
                                             <option>Female</option>
                                             <option>Other</option>
                                           </select>
                                           @if($errors->has('gender'))
                                           <div class="error" style="color: red">{{ $errors->first('gender') }}</div>
                                           @endif
                                       </div>
                                    </div>

                                   <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Bio<span class="red"></span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                       <textarea name="bio" class="form-control" id="exampleFormControlTextarea1" rows="3">{{$data->bio ?? ''}}    {{old('bio')}}</textarea>
                                       @if($errors->has('bio'))
                                       <div class="error" style="color: red">{{ $errors->first('bio') }}</div>
                                       @endif
                                    </div>
                                </div>
                                   
                                <div class="item form-group col-12">
                                 <label class="col-form-label col-md-3 col-sm-3 label-align">Profile Image<span class="red">*</span></label>
                                 <div class="col-md-6 col-sm-6">
                                   <input class="custom-file-label" name="profile_image" type="file" accept="image/*" onchange="readURL(this)" >
                                   @if($errors->has('profile_image'))
                                     <div class="error" style="color: red">{{ $errors->first('profile_image') }}</div>
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