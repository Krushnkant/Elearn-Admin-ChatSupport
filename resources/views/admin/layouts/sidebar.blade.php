<aside class="main-sidebar sidebar-dark-primary elevation-4">
   <!-- Brand Logo -->
   <a href="{{ url('admin/dashboard') }}" class="brand-link">
   <img src="{{ asset('public/Admin/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
   <span class="brand-text font-weight-light">{{ \Config::get('constants.APP_name') }}</span>
   </a>
   <!-- Sidebar -->
   <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
         <div class="image">
            <img src="{{ auth()->user()->profile_photo_path }}" class="img-circle elevation-2" alt="User Image">
         </div>
         <div class="info">
            <a href="{{ url('admin/dashboard') }}" class="d-block">{{ auth()->user()->name }}</a>
         </div>
      </div>
      <style type="text/css">
         .active {
            background: #585050;
         }

         .nav-sidebar li>a {
            position: relative;
         }
      </style>
      <!-- Sidebar Menu -->
      
      <nav class="mt-2">
         <ul class="nav nav-pills nav-sidebar flex-column sidebar-menu" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item has-treeview {{ (request()->is('admin/dashboard') || request()->is('admin/dashboard/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/dashboard') }}" class="nav-link">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                     Dashboard
                  </p>
               </a>
            </li>
            <li class="nav-item has-treeview {{ (request()->is('admin/category') || request()->is('admin/category/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/category') }}" class="nav-link">
                  <i class="nav-icon fa fa-tag" aria-hidden="true"></i>
                  <p class=" ml-2">
                     Category
                  </p>
               </a>
            </li>

            <li class="nav-item has-treeview {{ (request()->is('admin/courses') || request()->is('admin/courses/*') || request()->is('admin/chapters/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/courses') }}" class="nav-link">
                  <i class="nav-icon fa fa-certificate" aria-hidden="true"></i>
                  <p class=" ml-2">
                     Course
                  </p>
               </a>
            </li>

            <li class="nav-item has-treeview {{ (request()->is('admin/users') || request()->is('admin/users/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/users') }}" class="nav-link">
                  <i class="nav-icon fa fa-users" aria-hidden="true"></i>
                  <p class=" ml-2">
                     Users
                  </p>
               </a>
            </li>

            <li class="nav-item has-treeview {{ (request()->is('admin/ebooks') || request()->is('admin/ebooks/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/ebooks') }}" class="nav-link">
                  <i class="nav-icon fa fa-book" aria-hidden="true"></i>
                  <p class=" ml-2">
                     Ebook
                  </p>
               </a>
            </li>

            <li class="nav-item has-treeview {{ (request()->is('admin/repors') || request()->is('admin/repors/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/repors') }}" class="nav-link">
                  <i class="nav-icon fa fa-book" aria-hidden="true"></i>
                  <p class=" ml-2">
                     Reports
                  </p>
               </a>
            </li>

            <!-- <li class="nav-item has-treeview {{ (request()->is('admin/questions') || request()->is('admin/questions/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/assessments/16/questions') }}" class="nav-link">
                  <i class="nav-icon fas fa-file-csv" aria-hidden="true"></i>
                  <p class=" ml-2">
                     Import CSV
                  </p>
               </a>
            </li> -->
            <!-- <li class="nav-item has-treeview {{ (request()->is('admin/videos') || request()->is('admin/videos/*')) ? 'active' : ''}}">
               <a href="{{ url('admin/videos') }}" class="nav-link">
                  <i class="nav-icon fas fa-file-csv" aria-hidden="true"></i>
                  <p class=" ml-2">
                    Video
                  </p>
               </a>
            </li> -->
            
           
         </ul>
      </nav>
      <!-- /.sidebar-menu -->
   </div>
   <!-- /.sidebar -->
</aside>