@extends('admin.layouts.master')
  @section('css')
    <!-- <link rel="stylesheet" href="{{ asset('public/Admin/DataTables/css/dataTables.bootstrap.min.css') }}"> -->
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  @endsection
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $title }}
      </h1>
      <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables</a></li>
        <li class="active">Data tables</li>
      </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <!-- <h3 class="card-title">DataTable with default features</h3> -->
                <div class="box-tools float-right">
                  <a href="{{url('admin/assessments/'.$assessment_id.'/questions/create')}}" class="btn btn-success" style="padding-bottom: 3px;"> <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp; Add</a>
                  <a href="{{url('admin/assessments/'.$assessment_id.'/import-csv')}}" class="btn btn-success" style="padding-bottom: 3px;"> <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp; Import CSV</a>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="question-list" class="table table-bordered table-striped">
                <input type="hidden" name="data_table_name" id="data_table_name" value="question-list">
                <input type="hidden" name="table_name" id="table_name" value="questions">
                  <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Set type</th>

                    <th>Category Name</th>
                    <th>Sub Category Name</th>
                    <th>Question Type</th>
                    <th>Question</th>
                    <th>status</th>
                    <th>Created At</th>
                    <th>Action</th>
                    <!-- <th>Platform(s)</th>
                    <th>Engine version</th>
                    <th>CSS grade</th> -->
                  </tr>
                  </thead>
                  
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  
@endsection

@section('js')

<!-- <script type="text/javascript" src="{{ asset('public/Admin/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/Admin/DataTables/js/dataTables.bootstrap.min.js') }}"></script> -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="{{ asset('public/Admin/my-script.js') }}"></script>

<script type="text/javascript">

$(document).ready(function () {
    if ($('#question-list').length > 0) {
    var tableData = $('#question-list').DataTable({
            //stateSave: true,
            processing: true,
            serverSide: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: 'lBfrtip',
            language: {
                searchPlaceholder: "Search..."
            },
            buttons: [
                /*{
                    extend: 'csv',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },*/
            ],
            ajax: {
                url: "{{ url('admin/assessments/'.$assessment_id.'/questions') }}",
                type: 'GET',
            },
            "fnDrawCallback": function (oSettings) {
                
                $('body').off('click', '[id^="changeStatus-"]').on('click', '[id^="changeStatus-"]', function (e) {
                    var self = $(this);
                    var tbl = 'questions';
                    var id = $(this).attr('id').split('-')[1];
                    var status = $(this).attr('id').split('-')[2];

                    var msgStatus = status == 'Active' ? 'Inactive' : 'Active';
                    var msgStatus2 = status == 'Active' ? 'Inactivated' : 'Activated';

                    swal({
                        title: "Are you sure?",
                        text: "You want to " + msgStatus.toLowerCase() + " this record !!",
                        type: "warning",
                        confirmButtonText: 'Yes, ' + msgStatus.toLowerCase() + ' it!',
                        cancelButtonText: "No, cancel please!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then(function (value) {
                        if (value == 1) {
                            $.post(SITEURL + "/admin/change-status", {table: tbl, id: id, _token: '{{csrf_token()}}'},
                                    function (data) {
                                        if (data == '1') {
                                            if (status == 'Active') {
                                                self.attr('id', 'changeStatus-' + id + '-Inactive-').removeClass('btn-success').addClass('btn-danger').html("<i class='fa fa-thumbs-down'> Inactive </i>");
                                            } else {
                                                self.attr('id', 'changeStatus-' + id + '-Active-').removeClass('btn-danger').addClass('btn-info').html("<i class='fa fa-thumbs-up'> Active</i>");
                                            }
                                        }
                                    });
                            swal(msgStatus + "!", "Your record has been " + msgStatus2.toLowerCase() + "!", "success");
                        } else {
                             swal("Cancelled", "Your record is safe :)", "error"); 
                        }

                    });

                });

            },
            columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false,searchable: false},
              {data: 'set_type', name: 'set_type', 'visible': true, 'defaultContent': '--'},
              {data: 'category.type', name: 'category.type', 'visible': true, 'defaultContent': '--'},
              {data: 'category.name', name: 'category.name', 'visible': true, 'defaultContent': '--'},
              {data: 'question_type', name: 'question_type', 'visible': true,searchable: true},
              {data: 'title', name: 'title'},           
              {data: 'status', name: 'status','visible': true, "width": "10%" },
              {data: 'created_at', name: 'created_at','visible': true },
              {data: 'action', name: 'action', orderable: false},
            ],
            order: [[0, 'desc']]
        });

        
    }
});

</script>

@endsection