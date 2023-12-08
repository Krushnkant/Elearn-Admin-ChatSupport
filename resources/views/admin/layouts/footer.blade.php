<footer class="main-footer">
  <strong>Copyright &copy; {{ date('Y') }} <a href="{{ url('admin/dashboard') }}">{{ \Config::get('constants.APP_name') }}</a>.</strong>
  All rights reserved.
</footer>

</div>
<!-- jQuery -->
      <!-- <script src="{{ asset('public/Admin/plugins/jquery/jquery.min.js') }}"></script> -->
      <!-- jQuery UI 1.11.4 -->
      <!-- <script src="{{ asset('public/Admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script> -->
      <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
      <script>
         $.widget.bridge('uibutton', $.ui.button)
      </script>
      <!-- Bootstrap 4 -->
      <!-- <script src="{{ asset('public/Admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> -->
      <!-- ChartJS -->
      <script src="{{ asset('public/Admin/plugins/chart.js/Chart.min.js') }}"></script>
      <!-- Sparkline -->
      <script src="{{ asset('public/Admin/plugins/sparklines/sparkline.js') }}"></script>
      <!-- JQVMap -->
      <!-- <script src="{{ asset('public/Admin/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
      <script src="{{ asset('public/Admin/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script> -->
      <!-- jQuery Knob Chart -->
      <!-- <script src="{{ asset('public/Admin/plugins/jquery-knob/jquery.knob.min.js') }}"></script> -->
      <!-- daterangepicker -->
      <script src="{{ asset('public/Admin/plugins/moment/moment.min.js') }}"></script>
      <script src="{{ asset('public/Admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
      <!-- Tempusdominus Bootstrap 4 -->
      <script src="{{ asset('public/Admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
      <!-- Summernote -->
      <script src="{{ asset('public/Admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
      <!-- overlayScrollbars -->
      <script src="{{ asset('public/Admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
      <!-- AdminLTE App -->
      <script src="{{ asset('public/Admin/dist/js/adminlte.js') }}"></script>
      <!-- AdminLTE for demo purposes -->
      <script src="{{ asset('public/Admin/dist/js/demo.js') }}"></script>
      <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
      <!-- <script src="{{ asset('public/Admin/dist/js/pages/dashboard.js') }}"></script> -->
      <script src="{{asset('public/Admin/dist/sweetalert.min.js')}}"></script>
      <script src="{{asset('public\Admin\plugins\select2\js\select2.js')}}"></script>
      @yield('js')

</body>
</html>