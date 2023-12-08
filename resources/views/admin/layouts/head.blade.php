<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <title>{{ !empty($title) ? $title : '' }} | {{ \Config::get('constants.APP_name') }}</title>
      <!-- Google Font: Source Sans Pro -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="{{ asset('public/Admin/plugins/fontawesome-free/css/all.min.css') }}">
      <!-- Ionicons -->
      <!-- <link rel="stylesheet" href="{{ asset('public/Admin/https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css') }}"> -->
      <!-- Tempusdominus Bootstrap 4 -->
      <link rel="stylesheet" href="{{ asset('public/Admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
      <!-- iCheck -->
      <link rel="stylesheet" href="{{ asset('public/Admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
      <!-- JQVMap -->
      <link rel="stylesheet" href="{{ asset('public/Admin/plugins/jqvmap/jqvmap.min.css') }}">
      <!-- Theme style -->
      <link rel="stylesheet" href="{{ asset('public/Admin/dist/css/adminlte.min.css') }}">
      <!-- overlayScrollbars -->
      <link rel="stylesheet" href="{{ asset('public/Admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
      <!-- Daterange picker -->
      <link rel="stylesheet" href="{{ asset('public/Admin/plugins/daterangepicker/daterangepicker.css') }}">
      <!-- summernote -->
      <link rel="stylesheet" href="{{ asset('public/Admin/plugins/summernote/summernote-bs4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('public\Admin\plugins\select2\css\select2.css') }}">
      <link rel="stylesheet" href="{{ asset('public\Admin\plugins\select2\css\select2.min.css') }}">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet">
      <link rel="stylesheet" href="{{ asset('public\Admin\my-style.css') }}">

      <!-- <script src="{{ asset('public/Admin/plugins/jquery/jquery.min.js') }}"></script> -->
      <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
      <script src="{{ asset('public/Admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
      <script src="{{ asset('public/Admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  
  @yield('css')
</head>

<script type="text/javascript">
  var SITEURL = '{{URL::to('')}}';
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
</script>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">