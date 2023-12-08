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
                    <form role="form" name="add_userss_form" id="add_userss_form" action="{{ url('admin/users/store') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="box-body col-md-12">
                         
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter name">
                                    @if ($errors->has('name'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('name') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="email">Email<span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
                                    @if ($errors->has('email'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('email') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="mobile_number">Mobile Number<span class="text-danger">*</span></label>
                                    <input type="text" name="mobile_number" class="form-control" id="mobile_number" placeholder="Enter mobile number">
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
                                      <input type="radio" name="status" id="active" autocomplete="off" value="1" checked> Active
                                    </label>
                                    <label class="btn btn-secondary">
                                      <input type="radio" name="status" id="inactive" autocomplete="off" value="0"> Inactive
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
                                      <input type="radio" name="planid" id="active" autocomplete="off" value="1" checked> Free
                                    </label>
                                    <label class="btn btn-secondary">
                                      <input type="radio" name="planid" id="inactive" autocomplete="off" value="2"> Silver
                                    </label>
                                    <label class="btn btn-secondary">
                                      <input type="radio" name="planid" id="inactive" autocomplete="off" value="3"> Golden
                                    </label>
                                  </div>

                                    
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label for="mobile_number">Expiry Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" id="end_date" value="" placeholder="Enter Expiry Date">
                                    @if ($errors->has('end_date'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('end_date') }}
                                    </p>
                                    @endif
                                </div>



                                <div class="form-group col-md-6">
                                    <label for="profile_photo_path">Profile Picture<span class="text-danger">*</span></label>
                                    <input type="file" name="profile_photo_path" id="profile_photo_path" class="form-control">
                                    @if ($errors->has('profile_photo_path'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('profile_photo_path') }}
                                    </p>
                                    @endif
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

@endsection