@extends('admin.layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css">
<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
         
          <div class="x_content">

            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
               <form id="faqs-form" class="form-horizontal" method="post"  action="@if(isset($data)){{route('admin.product.store',$data->id)}}@else{{route('admin.product.store')}}@endif">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                  <fieldset>
                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">
                           <div class="x_title">
                           <h2>Faq's</h2>
                           <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                           </ul>
                           <div class="clearfix"></div>
                              </div>

                                <div class="x_content">
                                <br/>
                                <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align ">Question <span class="red">*</span> </label>
                                <div class="col-md-6 col-sm-6  ">
                                            <input class="form-control" type="text" value="{{$data->question ?? ''}}" placeholder="Question" name="question">
                                            @if($errors->has('question'))
                                            <div class="error" style="color: red">{{ $errors->first('question') }}</div>
                                            @endif
                               </div>
                               </div>

                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align">Answer<span class="red">*</span></label>
                                        <div class="col-md-6 col-sm-6">
                                         <textarea name="answer" id="editor" rows="10">{{$data->answer ?? ''}}</textarea>
                                         @if($errors->has('answer'))
                                         <div class="error" style="color: red">{{ $errors->first('answer') }}</div>
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
<script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
<script>
   ClassicEditor
       .create(document.querySelector('#editor'))
       .then(editor => { console.log(editor); })
       .catch(error => { console.error(error); });
</script>
@stop
