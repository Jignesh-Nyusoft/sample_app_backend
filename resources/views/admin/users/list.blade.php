@extends('admin.layouts.default')
@section('content')

<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                    <h2>Users List</h2>
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
                                    {{-- <tr role="row" class="filter">
                                        <th></th>
                                        <th></th>
                                        <th><input type="text" class="form-control" name="organization_name" id="organization_name" autocomplete="off" placeholder="Search by Organization"  value="{{ (isset($_GET['organization_name']) && !empty($_GET['organization_name'])) ? $_GET['organization_name'] : '' }}"></th>
                                        <th></th>

                                    </tr> --}}

                                    <tr>
                                        <th>Id</th>
                                        
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>

                                </thead>

                                <tbody>
                                  
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Delete User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure to delete this user?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          <a class="btn btn-danger" id="delete-url">Yes</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->


  <!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Restore User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure to restore this user?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          <a class="btn btn-danger" id="restore-url">Yes</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->

  <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        {{-- Modal content --}}
      </div>
    </div>
  </div>


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
{{-- <script type="text/javascript" src="{{ asset('datatable/js/datatable/jszip.min.js') }}"></script> --}}

<script>

    $(function () {
        var oTable = $('#user_table').DataTable({
        ordering: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        searching: true,
        "order": [[0, "desc"]],
        ajax: {
            url: "{!! route('admin.users.data') !!}",
            data: function (d) {
              // d.full_name = $('input[name=full_name]').val();
              // d.organization_name = $('input[name=organization_name]').val();
            }
        },

        columns: [
        {data: 'id', name: 'id'},
        {data: 'first_name', name: 'first_name'},
        {data: 'last_name', name: 'last_name'},
        {data: 'mobile', name: 'mobile'},
        {data: 'email', name: 'email'},
        {data: 'status', name: 'status'},
        {data: 'action', name: 'action'},
        ],
            dom: 'lBfrtip',
                buttons: [
                {
                extend: 'excel',
                title: 'User List',
                exportOptions: {
                    columns: [0,2,3,4]
                }
            },
            {
            extend: 'pdf',
            title: 'User List',
            customize: function (doc) {
                    doc.defaultStyle.fontSize = 10; //2, 3, 4,etc
                    doc.styles.tableHeader.fontSize = 12; //2, 3, 4, etc
                    doc.defaultStyle.alignment = 'left';
                    doc.styles.tableHeader.alignment = 'left';
                    doc.styles.tableHeader.padding = 10;
                    doc.content[1].table.widths = [ '5% ', '30%', '30%', '35%'];
                },
                exportOptions: {
                    columns: [0,2,3,4]
                }
            }
            ],

    });

$('#full_name').on('keyup', function (e) {
  oTable.draw();
  e.preventDefault();
});

// $('#organization_name').on('keyup', function (e) {
//   oTable.draw();
//   e.preventDefault();
// });
});

</script>

<script type="text/javascript">

    $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var url = button.data('url');
      var modal = $(this);
      modal.find('.modal-footer #delete-url').attr('href',url);
    });
  
    $('#restoreModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var url = button.data('url');
      var modal = $(this);
      modal.find('.modal-footer #restore-url').attr('href',url);
    });
  
    $('#viewModal').on('show.bs.modal', function(event) {
  
      var button = $(event.relatedTarget);
      var dataURL = button.data('url');
  
      $('.modal-content').load(dataURL,function(){
          // $('#viewModal').modal('show');
        });
  
    });
  
  </script>


<script type="text/javascript">
    $(document).on('click','.change_status',function(){
      var url = $(this).data('url');
      $.ajax({
        type: 'GET',
        url: url,
        data: '',
        success: function(response){
          toastr[response.msgType](response.msg, response.msgHead);
          /*swal({
            title: response.msgType,
            text: response.msg,
            icon: "success",
            button: "OK",
          });*/
        }
      });
    });
  </script>



<script>
$(document).ready(function() {
  var csrfToken = $('meta[name="csrf-token"]').attr('content');

  
  $(document).on('change', '.custom-control-input', function() {
    var isChecked = $(this).is(':checked');
    var id = $(this).data('id');

    var data = {
      id: id,
      status: isChecked
    };

    $.ajax({
      url: '{{route('users.status-update')}}',
      method: 'POST',           
      contentType: 'application/json',
      data: JSON.stringify(data),
      headers: {
        'X-CSRF-TOKEN': csrfToken 
      },
      success: function(response) {
        console.log('Status updated successfully:', response);
        document.location.reload();
      },
      error: function(xhr, status, error) {
        console.error('Error updating status:', error);
      }
    });
  });
});
</script>

@stop