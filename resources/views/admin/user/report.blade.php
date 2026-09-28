@extends('admin.layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
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
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-body">
              <table id="users-list" class="table table-bordered table-striped">
                <input type="hidden" name="data_table_name" id="data_table_name" value="users-list">
                <input type="hidden" name="table_name" id="table_name" value="users">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Passing Percentage</th>
                    <th>Total Scored</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Process group wise report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="width:100%;">
          <canvas id="canvas"></canvas>
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

<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="{{ asset('public/Admin/my-script.js') }}"></script>

<script type="text/javascript">

  $(document).ready(function () {

    $(document).on("click", ".edit-data", function () {
      $("#bodytable").html('');
      $.ajax({
        url: "{{ url('admin/repors/905/test-results') }}",
        method: "GET",
        dataType: "json",
        success: function (data) {
          $.each(data.categoryWiseReport, function (key, value) {
            var html = ' <tr>';
            html += '<td><a class="showhr" href="javascript:void(0)"><i class="fa fa-plus" aria-hidden="true"></i></a></td>';
            html += '<td class="text-left mainCat">' + value.title + '</td>';
            html += '<td>' + value.totalQuestion + '</td>';
            html += '<td>' + value.correctQuestion + '</td>';
            html += '<td class="catScores">' + value.scored.toFixed(2) + ' %</td>';
            html += '</tr>';
            $.each(value.category, function (key, value1) {
              html += ' <tr class="aser" style="display: none;">';
              html += '<td></td>';
              html += '<td class="text-left">' + value1.name + '</td>';
              html += '<td>' + value1.totalQuestion + '</td>';
              html += '<td>' + value1.correctQuestion + '</td>';
              html += '<td class="catScores">' + value1.scored.toFixed(2) + ' %</td>';
              html += '</tr>';
            });
            $("#bodytable").append(html);
          });
          $("#exampleModal").modal("show");
        }
      });
    });

    if ($('#users-list').length > 0) {
      $('#users-list').DataTable({
        processing: true,
        serverSide: true,
        dom: 'lBfrtip',
        language: {
          searchPlaceholder: "Search..."
        },
        buttons: [],
        ajax: {
          url: "{{ url('admin/repors') }}",
          type: 'GET',
        },
        columns: [
          { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
          { data: 'name', name: 'name', 'visible': true, 'defaultContent': '--' },
          { data: 'passing_percentage', name: 'passing_percentage', 'visible': true, searchable: true },
          { data: 'total_scored', name: 'total_scored', 'visible': true, searchable: true },
          { data: 'status', name: 'status', 'visible': true, searchable: true },
          { data: 'created_at', name: 'created_at', 'visible': true },
          { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'desc']]
      });
    }
  });

</script>

<script type="text/javascript" src="https://cdn2.hubspot.net/hubfs/476360/Chart.js"></script>
<script type="text/javascript" src="https://cdn2.hubspot.net/hubfs/476360/utils.js"></script>
<script type="text/javascript">
  var cats = $(".mainCat").map(function () { return $(this).html(); }).get();
  var scores = 48;
  var passingPerc = 70;
  var config = {
    type: 'line',
    data: {
      labels: cats,
      datasets: [{
        label: passingPerc + '% Passing Percentage',
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
          scaleLabel: {
            display: true,
            labelString: ''
          },
          ticks: {
            min: 0,
            max: 100,
            stepSize: 20
          }
        }]
      }
    }
  };

  $(document).on("click", ".showhr", function () {
    $(this).closest('tr').nextUntil("tr:has(.showhr)").toggle("slow", function () { });
  });
  window.onload = function () {
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, config);
  };

</script>

@endsection
