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
               <form id="user-form" class="form-horizontal" method="Post"  action="@if(!empty($data) && isset($data)){{route('categories.store',$data->id)}}@else{{route('categories.store')}}@endif" enctype="multipart/form-data">
                 
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                  @if (isset($data) && !empty($data))
                  <input type="hidden" name="id" value="{{$data->id}}"/>
                  @endif
                  <fieldset>
                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">
                            <div class="x_title">
                                 <h2>@if(isset($data)){{'Edit Category'}}@else{{'Add Category'}}@endif</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                 <li><a class="collapse-link"><i cla ss="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                              <div class="clearfix"></div>
                              </div>

                                <div class="x_content">
                                    <br/>
                                    <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Name<span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                    <input class="form-control" type="text" value="@if(isset($data->name)){{$data->name}}@endif" placeholder="Please enter Name" name="name">
                                    @if($errors->has('name'))
                                    <div class="error" style="color: red">{{ $errors->first('name') }}</div>
                                    @endif
                                 </div>
                                 </div>
    
                                 {{-- <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Parent Category<span class="red">*</span> </label>
                                    <div class="col-sm-6 col-sm-3">
                                       <select name="parent_id" class="form-control form-control-m" 
                                       @if (isset($data) && !empty($data)) disabled @endif>
                                       <option value="">Select</option>
                                   
                                       @forelse ($category as $list)
                                           <option value="{{ $list->id }}" 
                                               {{ isset($data) && $data->parent_id == $list->id ? 'selected' : '' }}>
                                               {{ $list->name }}
                                           </option>
                                       @empty
                                           <option value="">No categories available</option>
                                       @endforelse
                                   </select>
                                    </div>
                                 </div> --}}

                                
                                    <div class="item form-group col-12">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align">Category Image<span class="red"></span></label>
                                       <div class="col-md-12 col-sm-6">
                                         <input class="custom-file-label" name="image" type="file" accept="image/*" onchange="readURL(this)" >
                                         @if($errors->has('image'))
                                           <div class="error" style="color: red">{{ $errors->first('image') }}</div>
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
                                         @if($errors->has('color_code'))
                                          <div class="error" style="color: red">{{ $errors->first('color_code') }}</div>
                                          @endif
                                       </div>
                                    </div>
                                    <img id="img-preview" src="@if(isset($data) && !empty($data->image)) {{url($data->image ?? '')}}@endif"  style="margin-top: -13%; margin-left: 76%; height: 92px;"/> 

                                </div>
                           </div>
                        </div>
                     </div>
                  </fieldset>                    
                     <div class="ln_solid"></div>
                     <fieldset>
                        <div class="form-group row">
                              <div class="col-md-7 col-sm-7 offset-md-5">
                                 <a href="{{route('categories.index')}}" class="btn btn-warning">Discard</a>
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
    // Clear the image preview by setting src to a blank string
    $("#img-preview").attr("src", ""); 
    // Optionally, you can set the alt attribute to provide a visual cue that no image is available
    $("#img-preview").attr("alt", "No image selected");
  }
}

</script>

<script type="text/javascript">
    




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
    
 </script>
@stop