<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="cache-control" content="private, max-age=0, no-cache">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="0">
  <title>Admin Login | {{ \Config::get('constants.APP_name') }}</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('public/Admin/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('public/Admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('public/Admin/dist/css/adminlte.min.css') }}"> </head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo"> <a href="../../index2.html"><b>{{ \Config::get('constants.APP_name') }}</b></a> </div>
    
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <form action="{{ url('login') }}" method="post" name="login_form" id="login_form"> @csrf
          <div class="input-group mb-3">
            <input id="email" class="form-control" type="email" name="email" :value="old('email')" autofocus placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text"> <span class="fas fa-envelope"></span> </div>
            </div>
          </div> 
           @error('email')
              <span class="form__error text-danger">{{ $message }}</span>
          @enderror  
          <div class="input-group mb-3">
            <input id="password" class="form-control" type="password" name="password" autocomplete="current-password" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text"> <span class="fas fa-lock"></span> </div>
            </div>
          </div> 

          @error('password')
              <span class="form__error text-danger">{{ $message }}</span>
          @enderror   

          {!! $errors->login->first('password', '<span class="form__error text-danger">:message</span>') !!}

          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember"> Remember Me </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
  <!-- jQuery -->
    <script src="{{ asset('public/Admin/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('public\Admin\plugins\jquery-validation\jquery.validate.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('public/Admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('public/Admin/dist/js/adminlte.min.js') }}"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      $("form[name='login_form1']").validate({
        rules: {
          email: {
            required: true
          },
          password: {
            required: true
          },
        },
        messages: {
          email: {
            required: "Please enter email"
          },
          password: {
            required: "Please enter  password"
          },
        },

        errorPlacement: function(error, element){
          if(element.attr("name") == "ad_type" || element.attr("name") == "page_type[]") {
            error.insertAfter(element.next('.select2'));

           }else {
            error.insertAfter(element);
           }
        },
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
  </script>
</body>

</html>