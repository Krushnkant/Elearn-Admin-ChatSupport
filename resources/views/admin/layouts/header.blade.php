<style>
  /* Popper.js (loaded via bootstrap.bundle.js) repositions this dropdown with
     its own inline transform on open, and on wide-table admin pages its
     boundary math places it off the visible viewport. Pin it to the viewport
     itself so it renders identically on every page. */
  .navbar-nav .user-menu.dropdown .dropdown-menu{
    position: fixed !important;
    top: 57px !important;
    right: 10px !important;
    left: auto !important;
    transform: none !important;
    width: 320px !important;
    max-width: calc(100vw - 20px) !important;
  }
</style>
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
     <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              <img src="{{ auth()->user()->profile_photo_path }}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
              <!-- User image -->
              <li class="user-header">
                <img src="{{ auth()->user()->profile_photo_path }}" class="img-circle" alt="User Image">

                <p>
                  {{ auth()->user()->name }}
                </p>
              </li>

              <!-- Menu Footer-->
              <li class="user-footer" style="display:flex;box-sizing:border-box;">
                <div style="flex:1;min-width:0;box-sizing:border-box;">
                  <a href="{{ url('admin/change-password') }}" class="btn btn-default btn-flat" style="display:block;width:100%;box-sizing:border-box;white-space:nowrap;font-size:12.5px;padding-left:6px;padding-right:6px;">Change Password</a>
                </div>
                <div style="flex:1;min-width:0;box-sizing:border-box;">
                  <a href="{{ url('admin/log-out') }}" class="btn btn-default btn-flat" style="display:block;width:100%;box-sizing:border-box;white-space:nowrap;font-size:12.5px;padding-left:6px;padding-right:6px;">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
  </ul>
</nav>
<!-- /.navbar -->