@extends('admin.layouts.default')
@section('content')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css">
<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
         
          <div class="x_content">

            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
               <form id="faqs-form" class="form-horizontal" method="post"  action="@if(isset($data)){{route('cms.store',$data->id)}}@else{{route('cms.store')}}@endif">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                  <input type="hidden" name="id" value="{{ $data->id ?? ''}}" />
                  <fieldset>
                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">

                              <div class="x_title">
                                 <h2>Cms Create</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>

                                <div class="x_content">
                                    <br />
                                  
                                   
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align ">Title <span class="red">*</span> </label>
                                        <div class="col-md-6 col-sm-6  ">
                                            <input class="form-control" type="text" value="{{ old('title', $data->title ?? '') }}" placeholder="Please Enter Title" name="title">
                                            @if($errors->has('title'))
                                            <div class="error" style="color: red">{{ $errors->first('title') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                   
                                    <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Keyword <span class="red">*</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                          {{-- <textarea name="keyword"  rows="10">{{$data->keyword ?? ''}}</textarea> --}}
                                          <input class="form-control" type="text" value="{{ old('keyword', $data->keywords ?? '') }}"  placeholder="Please Enter keyword" name="keyword">
                                          @if($errors->has('keyword'))
                                           <div class="error" style="color: red">{{ $errors->first('keyword') }}</div>
                                           @endif
                                   </div>
                                   </div>


                                    <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">Content<span class="red">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                         <textarea name="content" id="editor" rows="10">{{$data->content ?? ''}}</textarea>
                                         @if($errors->has('content'))
                                         <div class="error" style="color: red">{{ $errors->first('content') }}</div>
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

                                </div>
                           </div>
                        </div>
                     </div>

                  </fieldset>                    

                     <div class="ln_solid"></div>

                     <fieldset>
                        <div class="form-group row">
                              <div class="col-md-7 col-sm-7 offset-md-5">
                                 <a href="{{route('cms.index')}}" class="btn btn-warning">Discard</a>
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
