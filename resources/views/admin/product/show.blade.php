@extends('admin.layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css">
<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12  ">
      <div class="x_panel">
         
        <div class="x_content">

     <h2>Seller Details - {{$data->user->id}}</h2>

          <div class="container">
              <form class="form">
              

                  <div class="row">
  
                  <div class="col">
                    <label for="">First Name</label>
                  <input  readonly type="text" class="form-control" value="{{$data->user->first_name  ?? ''}}" placeholder="Please Enter order number">
                  </div>
                  <div class="col">
                    <label for="">Last Name</label>
                  <input  readonly type="text" class="form-control" value="{{$data->user->last_name  ?? ''}}" placeholder="Please Enter order number">
                  </div>
                  <div class="col">
                    <label for="">Mobile Number</label>
                  <input  readonly type="text" class="form-control" value="{{$data->user->mobile  ?? ''}}" placeholder="Please Enter order number">
                  </div>
                
                  <div class="col">
                    <label for="">Email</label>
                  <input  readonly type="text" class="form-control" value="{{$data->user->email  ?? ''}}" placeholder="Please Enter order number">
                  </div>

                  <div class="col">
                    <label for="">Zip Code</label>
                  <input  readonly type="text" class="form-control" value="{{$data->user->zip_code  ?? ''}}" placeholder="Please Enter order number">
                  </div>
                
                </div>
                  </div>
                               
                  </div>
              </form>
              </div>


        <div class="x_panel">
         
          <div class="x_content">

       <h2>Product Details - {{$data->product_name}}</h2>

            <div class="container">
                <form class="form">
                   <div class="row">
                    
                    <div class="col">
                        <label for="">Product name</label>
                      <input  readonly type="text" class="form-control" value="{{$data->product_name ?? ''}}" placeholder="Please Enter Product Name">
                    </div>

                    <div class="col">
                        <label for="">Category Name</label>
                      <input readonly type="text" class="form-control" value="{{$data->category->name ?? ''}}" placeholder="Please Enter Category Name">
                    </div>

                    <div class="col">
                        <label for="">Brand Name</label>
                      <input readonly type="text" class="form-control" value="{{$data->brand->name ?? ''}}" placeholder="Please Enter Brand Name">
                    </div>
                    </div>


                    <div class="row">
                        <div class="col">
                            <label for="">Size</label>
                          <input readonly type="text" class="form-control" value="{{$data->size->name ?? ''}}" placeholder="Please Enter Size ">
                        </div>
    
                        <div class="col">
                            <div class="row">

                                <div class="col-2">
                                    <label for="colorInput"> Color  </label>
                                </div>
                              
                                <div class="col-2">
                                    <span style="background-color: {{$data->Color->color_code}}; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span>
                                </div>

                            </div>
                            
                            <input readonly type="text" class="form-control" value="{{$data->Color->name ?? 'No Data'}}" placeholder="Please Enter Color">
                       
                        </div>
                        <div class="col">
                            <label for="">Material</label>
                          <input readonly type="text" class="form-control" value="{{$data->material->name ?? 'No Data'}}" placeholder="Please Enter Material Name">
                        </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <label for="">Cloth type</label>
                              <input readonly type="text" class="form-control" value="{{$data->cloth_type ?? ''}}" placeholder="Please Select Cloth Type">
                            </div>
        
                            <div class="col">
                                <label for="colorInput"> Suitable For </label>
                                <input readonly type="text" class="form-control" value="{{$data->suitable->name ?? 'No Data'}}" placeholder="Please Enter Suitable For">
                           
                            </div>

                            <div class="col">
                                <label for="">Condition</label>
                              <input readonly type="text" class="form-control" value="{{$data->condition->name ?? 'No Data'}}" placeholder="Please Enter Condition">
                            </div>
                            </div>
    
                            <div class="row">
                                <div class="col">
                                    <label for="">Stock</label>
                                  <input readonly type="text" class="form-control" value="{{$data->stock ?? ''}}" placeholder="Please Enter Stock">
                                </div>
            
                                <div class="col">
                                    <label for="colorInput">Price </label>
                                    <input readonly type="text" class="form-control" value="{{$data->price ?? 'No Data'}}" placeholder="Please Enter Price">
                                </div>

                                <div class="col">
                                    <label for="colorInput">Status </label>
                                    <input readonly type="text" class="form-control" value="{{$data->status ?? 'No Data'}}" placeholder="Please Select Status">
                                </div>
                                </div>

                                
                  <div class="mb-3">
                  <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                  <textarea readonly class="form-control" id="exampleFormControlTextarea1" rows="3">{{$data->description ?? ''}}</textarea>
                </div>
                  
                <div class="row">
                    <div class="col">
                        <label for="">Default Image</label> <br>
                      <img style="width: 198px; border: double;" src="{{asset($data->image)}}" alt="">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <label for="">More Images</label> <br>
                       @if($data->ProductImages)
                       @foreach ($data->ProductImages as $images)
                       <img style="width: 146px; border: double;height: 99px;" src="{{asset($images->image)}}" alt="">  
                       @endforeach
                        @endif
                     
                </div>
                </div>
                </form>
                </div>
                </div>
                </div>
    </div>
</div>

@stop
@section('footer_scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
<script>
   ClassicEditor
       .create(document.querySelector('#editor'))
       .then(editor => { console.log(editor); })
       .catch(error => { console.error(error); });
</script>
@stop


<style>
  .form .row {
  margin-top: 10px;
}
label[for="colorInput"] {
  display: flex;
  gap: 5px;
  align-items: center;
}
@media only screen and (max-width: 767px){
  .form .col {
  width: 100%;
  flex-basis: 100%;
  margin-top: 10px;
}
}


</style>