@extends('admin.layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
@endsection
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>{{ $title }}</h1>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          @if (session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show shadow-sm auto" role="alert">
            <strong>Great!</strong> {{ session()->get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          @endif
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="box-tools float-right">
                <a href="{{url('admin/live-videos')}}" class="btn btn-secondary" style="padding-bottom: 3px;"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp; Back to Days</a>
                <a href="{{url('admin/live-videos/'.$day->id.'/links/create')}}" class="btn btn-success" style="padding-bottom: 3px;"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp; Add Video</a>
              </div>
            </div>
            <div class="card-body">
              <table id="livevideolinks-list" class="table table-bordered table-striped">
                <input type="hidden" name="data_table_name" id="data_table_name" value="livevideolinks-list">
                <input type="hidden" name="table_name" id="table_name" value="live_video_links">
                <thead>
                  <tr>
                    <th>Id</th>
                    <th>Order</th>
                    <th>Title</th>
                    <th>Video URL</th>
                    <th>Mins</th>
                    <th>Status</th>
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
@endsection

@section('js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ asset('public/Admin/my-script.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    if ($('#livevideolinks-list').length > 0) {
      var tableData = $('#livevideolinks-list').DataTable({
        processing: true,
        serverSide: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: 'lfrtip',
        language: { searchPlaceholder: "Search..." },
        ajax: { url: "{{ url('admin/live-videos/'.$day->id.'/links') }}", type: 'GET' },
        "fnDrawCallback": function (oSettings) {
          $('body').off('click', '[id^="changeStatus-"]').on('click', '[id^="changeStatus-"]', function (e) {
            var self = $(this);
            var tbl = 'live_video_links';
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
              icon: "warning", buttons: true, dangerMode: true,
            }).then(function (value) {
              if (value == 1) {
                $.post(SITEURL + "/admin/change-status", { table: tbl, id: id, _token: '{{csrf_token()}}' },
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
          { data: 'id', name: 'id', 'visible': false },
          { data: 'sort_order', name: 'sort_order', 'visible': true },
          { data: 'title', name: 'title', 'visible': true },
          { data: 'video_url', name: 'video_url', 'visible': true },
          { data: 'duration_mins', name: 'duration_mins', 'visible': true },
          { data: 'status', name: 'status', 'visible': true },
          { data: 'action', name: 'action', orderable: false },
        ],
        order: [[1, 'asc']]
      });
    }
  });
</script>
@endsection
