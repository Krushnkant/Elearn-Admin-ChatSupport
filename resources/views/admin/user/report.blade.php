@extends('admin.layouts.master')
  @section('css')
    <!-- <link rel="stylesheet" href="{{ asset('public/Admin/DataTables/css/dataTables.bootstrap.min.css') }}"> -->
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  @endsection
@section('content')

<style type="text/css">
  @keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}
  .chartjs-render-monitor{animation:chartjs-render-animation 1ms}
  .chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}
  .chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}
  .chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{ $title }}
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

            <div class="card">
              
              <!-- /.card-header -->
              <div class="card-body">
                <table id="users-list" class="table table-bordered table-striped">
                  <input type="hidden" name="data_table_name" id="data_table_name" value="users-list">
                  <input type="hidden" name="table_name" id="table_name" value="users">
                  <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                   
                   <!--  <th>Total Attempt</th> -->
                    <th>Passing Percentage</th>
                     <th>Total Scored</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
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




<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Process group wise report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="width:100%;">
          <div class="chartjs-size-monitor">
            <div class="chartjs-size-monitor-expand">
              <div class=""></div>
            </div>
            <div class="chartjs-size-monitor-shrink">
              <div class=""></div>
            </div>
          </div>
          <canvas id="canvas" style="display: block; width: 1379px; height: 689px;" width="1379" height="689" class="chartjs-render-monitor"></canvas>
        </div>
        <div class="table-responsive">
           <table class="table reportTable text-center">
              <thead>
                <tr>
                  <th></th>
                  <th class="text-left">Process Group</th>
                  <th>Total Questions</th>
                  <th>Correct Questions</th>
                  <th>Percentage Scored</th>
                </tr>
              </thead>
              <tbody id="bodytable">
              </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
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

$(document).on("click", ".edit-data", function(){
     $("#bodytable").html('');
    var contactId = $(this).attr("id");
    $.ajax({
        url: "{{ url('admin/repors/905/test-results') }}",
        method: "GET",
      
        dataType: "json",
        success: function(data){
          var html = "";
         
          $.each( data.categoryWiseReport, function( key, value ) {
            html = ' <tr>';
                     html +='<td><a class="showhr" href="javascript:void(0)"><i class="fa fa-plus" aria-hidden="true"></i></a></td>';
                    html +='<td class="text-left mainCat">'+ value.title +'</td>';
                    html +='<td>'+ value.totalQuestion +'</td>';
                   html +='<td>'+ value.correctQuestion +'</td>';
                    html +='<td class="catScores">'+value.scored.toFixed(2)+' %</td>';
                 html +='</tr>';
                $.each( value.category, function( key, value1 ) {
                     html += ' <tr class="aser" style="display: none;">';
                     html +='<td></td>';
                    html +='<td class="text-left">'+ value1.name +'</td>';
                    html +='<td>'+ value1.totalQuestion +'</td>';
                   html +='<td>'+ value1.correctQuestion +'</td>';
                    html +='<td class="catScores">'+value1.scored.toFixed(2)+' %</td>';
                 html +='</tr>';
                 });

            $("#bodytable").append(html);
          });
          // console.log(html);
            // $("#name").val(data.name);
            // $("#email").val(data.email);
            // $("#description").val(data.description);
            // $("#contactUserId").val(data.userid);
            // $("#add").val("Update");
            $("#exampleModal").modal("show");

        }

    });

});


    if ($('#users-list').length > 0) {
    var tableData = $('#users-list').DataTable({
            //stateSave: true,
            processing: true,
            serverSide: true,
            //lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
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
                //url: "https://chatsupport.co.in/admin/repors",
                url: "{{ url('admin/repors') }}",
                type: 'GET',
            },
            "fnDrawCallback": function (oSettings) {
                
                $('body').off('click', '[id^="changeStatus-"]').on('click', '[id^="changeStatus-"]', function (e) {
                    var self = $(this);
                    var tbl = 'users';
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
              {data: 'name', name: 'name', 'visible': true, 'defaultContent': '--'},
              
              // {data: 'total_attempt', name: 'total_attempt', 'visible': true,searchable: true},
              {data: 'passing_percentage', name: 'passing_percentage', 'visible': true,searchable: true},
              {data: 'total_scored', name: 'total_scored', 'visible': true,searchable: true},
              {data: 'status', name: 'status', 'visible': true,searchable: true},
              {data: 'created_at', name: 'created_at','visible': true },
              {data: 'action', name: 'action', orderable: false},
            ],
            order: [[0, 'desc']]
        });

        
    }
});

</script>

<script type="text/javascript" src="https://cdn2.hubspot.net/hubfs/476360/Chart.js"></script>
<script type="text/javascript" src="https://cdn2.hubspot.net/hubfs/476360/utils.js"></script>
<script type="text/javascript">
  var cats = $(".mainCat").map(function(){return $(this).html();}).get();
  var scores = 48;
  var passingPerc = 70;
  var config = {
    type: 'line',
    data: {
      labels: cats,
      datasets: [{
        label: passingPerc +'% Passing Percentage',
        backgroundColor: '#FF0000',
        borderColor: '#FF0000',
        fill: false,
        data: [
          passingPerc,
          passingPerc,
          passingPerc
        ],
      }, {
        label: 'Actual Percentage Scored',
        backgroundColor: '#0d6efd',
        borderColor: '#0d6efd',
        fill: false,
        data: scores,
    
      }]
    },
    options: {
      responsive: true,
      title: {
        display: true,
        text: 'Exam Result Chart'
      },
      scales: {
        xAxes: [{
          display: true,
          scaleLabel: {
            display: true,
            labelString: 'Process Group'
          },
      
        }],
        yAxes: [{
          display: true,
          //type: 'logarithmic',
          scaleLabel: {
              display: true,
              labelString: ''
            },
            ticks: {
              min: 0,
              max: 100,

              // forces step size to be 5 units
              stepSize: 20
            }
        }]
      }
    }
  };

  $(document).on("click", ".showhr", function(){
     $(this).closest('tr').nextUntil("tr:has(.showhr)").toggle("slow", function() {});
  });
  window.onload = function() {
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, config);
  };

  // document.getElementById('randomizeData').addEventListener('click', function() {
  //   config.data.datasets.forEach(function(dataset) {
  //     dataset.data = dataset.data.map(function() {
  //       return randomScalingFactor();
  //     });

  //   });

  //   window.myLine.update();
  // });

  
</script>

@endsection