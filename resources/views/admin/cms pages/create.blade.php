@extends('admin.layouts.default')
@section('content')

<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
            <div class="x_content">
  
              <div class="tab-content" id="myTabContent">

                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                 <form id="user-form" class="form-horizontal" method="post"  action="@if(isset($data)){{route('admin.pages.update',$data->id)}}@endif" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    <div class="row">

                        <div class="col-md-12 col-sm-12 ">

                           <div class="x_panel">

                              <div class="x_title">

                                 <h2>Create New User</h2>

                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 
                                 <div class="clearfix"></div>

                              </div>

                                <div class="x_content">
                                    <br />
                                
                                <fieldset>
                                    @if (isset($data) && $data->slug == 'home')

                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align ">Page Title<span class="red">*</span> </label>
                                            <div class="col-md-6 col-sm-6  ">
                                                <input class="form-control" type="text" value="@if(isset($data->title)){{$data->title}}@endif" placeholder="Page Title" name="title">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align ">Title 1<span class="red">*</span> </label>
                                            <div class="col-md-6 col-sm-6  ">
                                                <input class="form-control" type="text" value="@if(isset($data->page_meta['title_1'])){{$data->page_meta['title_1']}}@endif" placeholder="Enter Title" name="title_1">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align ">Banner Image</label>
                                            <div class="col-md-6 col-sm-6  ">
                                                @if(isset($data->page_meta['banner_image']))
                                                    <img src="{{asset('/uploads/'.$data->page_meta['banner_image'])}}" height="100px">
                                                @endif
                                                <input class="form-control" type="file" name="banner_image" >
                                            </div>
                                        </div>

                                    @endif


                                    @if (isset($data) && $data->slug == 'about')
                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align ">Page Title<span class="red">*</span> </label>
                                            <div class="col-md-6 col-sm-6  ">
                                                <input class="form-control" type="text" value="@if(isset($data->title)){{$data->title}}@endif" placeholder="Page Title" name="title">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align ">About Title<span class="red">*</span> </label>
                                            <div class="col-md-6 col-sm-6  ">
                                                <input class="form-control" type="text" value="@if(isset($data->page_meta['about_title'])){{$data->page_meta['about_title']}}@endif" placeholder="Enter About Title" name="about_title">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align ">Banner Image</label>
                                            <div class="col-md-6 col-sm-6  ">
                                                @if(isset($data->page_meta['about_banner_image']))
                                                    <img src="{{asset('/uploads/'.$data->page_meta['about_banner_image'])}}" height="100px">
                                                @endif
                                                <input class="form-control" type="file" name="about_banner_image" >
                                            </div>
                                        </div>
                                    @endif
                                    </fieldset>
                                    
                                    <fieldset>
                                        <div class="text-right mt-3">
                                             <a href="{{route('admin.pages')}}" class="btn btn-warning">Discard</a>
                                             <button class="btn btn-success submit" type="submit">Save</button>
                                       </div>
                                    </fieldset>

                                </div>

                            </div>

                        </div>

                    </div>

                 </form>

                </div>

              </div>

            </div>
        </div>
    </div>
</div>

@stop
@section('footer_scripts')
<script src="{{ asset('vendor/ckeditor_full/ckeditor.js')}}"></script>
<script src="{{ asset('admin_assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/additional-methods.js') }}"></script>
@stop