@extends('admin.layouts.default')
@section('content')

<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                    <h2>CMS pages List</h2>
                    <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                    </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                                <table id="user_table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>slug</th>
                                        <th>Title</th>
                                        <th>Meta Title</th>
                                        <th>Add Date</th>
                                        <th>Actions</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    @forelse($data as $key=>$val)
                                    <tr class="gradeU">
                                        <td>{{$val->id}}</td>
                                        <td>{{$val->slug}}</td>
                                        <td>{!!$val->title!!}</td>
                                        <td>{{$val->meta_title}}</td>
                                        <td>{{$val->created_at}}</td>
                                        <td>
                                            {{-- <button data-id="{{$val->id}}" data-title="{{$val->title}}" data-description="{{$val->description}}"
                                                data-meta_title="{{$val->meta_title}}" data-meta_keywords="{{$val->meta_keywords}}" data-meta_description="{{$val->meta_description}}"
                                                data-toggle="modal" data-target="#viewModal" class="btn btn-info" title="View"><i class="fa fa-eye"></i></button> --}}
                                            <a href="{{route('admin.pages.edit',$val->id)}}" class="btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>
                                            {{-- <button data-url="{{route('admin.pages.delete',$val->id)}}" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button> --}}
                                          </td>
                                    </tr> 
                                    
                                    @endforeach     
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>   


@stop
@section('before_scripts')
@stop

@section('header_styles')
<!-- Datatables-->
<link rel="stylesheet" href="{{asset('admin_assets/vendors/datatables.net-bs4/css/dataTables.bootstrap4.css')}}">
<style>
  .user_img {
    height: 50px;
    width: 50px;
    border-radius: 50%;
  }
</style>

<link rel="stylesheet" href="{{ asset('admin_assets/vendors/datatables.net-bs4/css/dataTables.bootstrap4.css')}}">
<link rel="stylesheet" href="{{ asset('datatable/css/datatable/jquery.dataTables.min.css')}}">
<link rel="stylesheet" href="{{ asset('datatable/css/datatable/buttons.dataTables.min.css')}}">

@stop

@section('footer_scripts')

<script src="{{asset('admin_assets/vendors/datatables.net/js/jquery.dataTables.js')}}"></script>
<script src="{{asset('admin_assets/vendors/datatables.net-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script type="text/javascript" src="{{ asset('datatable/js/datatable/dataTables.buttons.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('datatable/js/datatable/pdfmake.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('datatable/js/datatable/buttons.html5.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('datatable/js/datatable/vfs_fonts.js')}}"></script>

<script>
       $(function () {
        $(document).ready( function () {
            $('#myTable').DataTable();
        } );
       })
</script>

@stop