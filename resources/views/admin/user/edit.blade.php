@extends('admin.layouts.master')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
      <h1>
        {{ $title }}
      </h1>
    </section>
    <section class="content">
    <div class="card-body">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary box-solid">
                <div class="card-header bg-primary"></div>
                <div class="box-body border border-primary">
                    <form role="form" name="add_course_form" id="add_course_form" action="{{ url('admin/users/update') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="id" value="{{ (!empty($user_info->id) ? ($user_info->id) : ('')) }}">
                        <div class="box-body col-md-12">
                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name', !empty($user_info->name) ? $user_info->name : '') }}" placeholder="Enter name">
                                    @if ($errors->has('name'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('name') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="email">Email<span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email', !empty($user_info->email) ? $user_info->email : '') }}" placeholder="Enter email">
                                    @if ($errors->has('email'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('email') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="mobile_number">Mobile Number<span class="text-danger">*</span></label>
                                    <input type="text" name="mobile_number" class="form-control" id="mobile_number" value="{{ old('mobile_number', !empty($user_info->mobile_number) ? $user_info->mobile_number : '') }}" placeholder="Enter mobile number">
                                    @if ($errors->has('mobile_number'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('mobile_number') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6 ">
                                  <label for="status">Status<span class="text-danger">*</span></label><br>
                                  <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary active">
                                      <input type="radio" name="status" id="active" autocomplete="off" value="1" {{ $user_info->status == 1 ? 'checked' : '' }}> Active
                                    </label>
                                    <label class="btn btn-secondary">
                                      <input type="radio" name="status" id="inactive" autocomplete="off" value="0" {{ $user_info->status == 0 ? 'checked' : '' }}> Inactive
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="password">Password<span class="text-danger">*</span></label>
                                    <input type="text" name="password" class="form-control" id="password" placeholder="Enter Password">
                                    <div id="button" class="btn btn-primary mt-1" onclick="genPassword()">Generate</div>
                                    <div  id="button" class="btn btn-primary mt-1" onclick="copyPassword()">Copy</div>
                                    @if ($errors->has('password'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('password') }}
                                    </p>
                                    @endif
                                </div>


                                <div class="form-group col-md-6 ">
                                  <label for="planid">Plan<span class="text-danger">*</span></label><br>
                                  <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary active">
                                      <input type="radio" name="planid" id="active" autocomplete="off" value="1" {{ $user_info->plan == 1 ? 'checked' : '' }} > Free
                                    </label>
                                    <label class="btn btn-secondary">
                                      <input type="radio" name="planid" id="inactive" autocomplete="off" value="2" {{ $user_info->plan == 2 ? 'checked' : '' }} > Silver
                                    </label>
                                    <label class="btn btn-secondary">
                                      <input type="radio" name="planid" id="inactive" autocomplete="off" value="3" {{ $user_info->plan == 3 ? 'checked' : '' }} > Golden
                                    </label>
                                  </div>
                                </div>
                                 <?php
                                    $end_user = date("Y-m-d", strtotime($user_info->end_date));
                                 ?>
                                 <div class="form-group col-md-6">
                                    <label for="end_date">Expiry Date<span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" id="end_date" value="{{ old('end_date', !empty($end_user) ? $end_user : '') }}" >
                                    @if ($errors->has('end_date'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('end_date') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="duration">Image<span class="text-danger">*</span></label>
                                    <input type="file" name="profile_photo_path" class="form-control" id="profile_photo_path">
                                    @if ($errors->has('profile_photo_path'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('profile_photo_path') }}
                                    </p>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    <img src="{{ $user_info->profile_photo_path }}" class="rounded" height="100px" width="150px;">
                                </div>
                              </div>

                               <div class="form-group col-md-12">
                                    <a href="{{ url('admin/users') }}" class="btn btn-danger">Cancel</a>
                                    <button id="btn-category" type="submit" class="btn btn-primary">Submit</button>
                                </div>    
                            </form> 
                        </div>
                    </div>
                </div>
            </div> 
        </div>   
    </section>
</div>

<script type="text/javascript">
 var password=document.getElementById("password");

 function genPassword() {
    var chars = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var passwordLength = 12;
    var password = "";
 for (var i = 0; i <= passwordLength; i++) {
   var randomNumber = Math.floor(Math.random() * chars.length);
   password += chars.substring(randomNumber, randomNumber +1);
  }
        document.getElementById("password").value = password;
 }

function copyPassword() {
  var copyText = document.getElementById("password");
  copyText.select();
  document.execCommand("copy");  
}
</script>

@endsection


@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'overview' );
        CKEDITOR.replace('mock_test');
        CKEDITOR.replace('course_outline');
    </script>    
@endsection