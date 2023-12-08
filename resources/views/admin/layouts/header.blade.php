<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
     <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
     </li>
     <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
     </li>
     <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
     </li> -->
  </ul>
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3">
     <!-- <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
           <button class="btn btn-navbar" type="submit">
           <i class="fas fa-search"></i>
           </button>
        </div>
     </div> -->
  </form>
  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
     <li class="dropdown user user-menu open">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <img src="{{ auth()->user()->profile_photo_path }}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{ auth()->user()->profile_photo_path }}" class="img-circle" alt="User Image">

                <p>
                  {{ auth()->user()->name }}
                </p>
              </li>
              
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="{{ url('admin/change-password') }}" class="btn btn-default btn-flat">Change Password</a>
                </div>
                <!-- <div class="pull-center">
                  <a href="javascript:void(0);" class="btn btn-default btn-flat">Update</a>
                </div> -->
                <div class="pull-right">
                  <a href="{{ url('admin/log-out') }}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
  </ul>
</nav>
<!-- /.navbar -->