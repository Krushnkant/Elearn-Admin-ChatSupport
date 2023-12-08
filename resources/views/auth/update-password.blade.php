@extends('admin.layouts.master')

@section('content')
   <div class="content-wrapper">
    <section class="content-header">
      <h1>
        {{ $title }}
      </h1>
    </section>
    <section class="content">
    <div class="card-body ">

    <div class="row">
      <div class="col-md-12">
        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm auto" role="alert">
          <strong>Greate!</strong> {{ session()->get('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if (session()->has('warning'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm auto" role="alert">
          <strong>Oops!</strong> {{ session()->get('warning') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
      </div>
    </div>

    <div class="row">
      <!-- left column -->
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="card-header bg-primary"></div>
        <div class="box-body border border-primary">
          <form role="form" name="change-password" id="change-password" action="{{ url('admin/change-password') }}" method="post" >
            {{ csrf_field() }}
            <div class="box-body col-md-12">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="name">Old Password<span class="text-danger">*</span></label>
                  <input type="password" name="old_password" class="form-control" id="old_password" placeholder="Enter Old Password" value=""></span>
                  @if ($errors->has('old_password'))
                  <p class="error">
                      <i class="fa fa-times-circle-o"></i>  {{ $errors->first('old_password') }}
                  </p>
                  @endif
                </div>
                <div class="form-group col-md-4">
                  <label for="title">Password<span class="text-danger">*</span></label>
                  <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" value="{{ old('password') }}">
                  @if ($errors->has('password'))
                  <p class="error text text-danger">
                      <i class="fa fa-times-circle-o"></i>  {{ $errors->first('password') }}
                  </p>
                  @endif
                </div>
            
                <div class="form-group col-md-4">
                  <label for="status">Confirm Password<span class="text-danger">*</span></label>
                  <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Enter confirm_password" value="{{ old('confirm_password') }}">
                  @if ($errors->has('confirm_password'))
                  <p class="error text text-danger">
                      <i class="fa fa-times-circle-o"></i>  {{ $errors->first('confirm_password') }}
                  </p>
                  @endif
                </div>
              </div>
            </div>
                  
            <div class="form-group col-md-12">
              <a href="{{ url('admin/dashboard') }}" class="btn btn-danger">Cancel</a>
              <button id="btn-category" type="submit" class="btn btn-primary">Submit</button>
            </div>    
          </form> 
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
  <script src="{{ asset('public/Admin/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('public\Admin\plugins\jquery-validation\jquery.validate.js') }}"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $("form[name='change-password']").validate({
        rules: {
          old_password: {
            required: true,
            maxlength: 25,
            minlength: 6
          },
          password: {
            required: true,
            minlength: 6,
            maxlength: 20,
          },
          confirm_password: {
            required: true,
            minlength: 6,
            maxlength: 20,
            equalTo: "#password"
          },
        },
        messages: {
          old_password: {
            required: "Please enter old password",
            minlength: "Your old password must be at least 6 characters long",
            maxlength: "Old password maximun length should be 20 character"
          },
          password: {
            required: "Please enter password",
            minlength: "Your password must be at least 6 characters long",
            maxlength: "Password maximun length should be 20 character"
          },
          confirm_password: {
            required: "Please enter  confirm password",
            minlength: "Your confirm password must be at least 6 characters long",
            maxlength: "Confirm password maximun length should be 20 character",
            equalTo: "Password and confirm password musr be same"
          },
        },
        
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
  </script>
@endsection