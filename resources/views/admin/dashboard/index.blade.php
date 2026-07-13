@extends('admin.layouts.master')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Row 1: primary stats -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ number_format($stats['students']) }}</h3>
                <p>Students</p>
              </div>
              <div class="icon"><i class="fas fa-user-graduate"></i></div>
              <a href="{{ url('admin/users') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ number_format($stats['courses']) }}</h3>
                <p>Courses</p>
              </div>
              <div class="icon"><i class="fas fa-book"></i></div>
              <a href="{{ url('admin/courses') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ number_format($stats['ebooks']) }}</h3>
                <p>E-books</p>
              </div>
              <div class="icon"><i class="fas fa-book-open"></i></div>
              <a href="{{ url('admin/ebooks') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ number_format($stats['questions']) }}</h3>
                <p>Questions</p>
              </div>
              <div class="icon"><i class="fas fa-question-circle"></i></div>
              <a href="{{ url('admin/courses') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <!-- Row 2: secondary stats -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner">
                <h3>{{ number_format($stats['categories']) }}</h3>
                <p>Categories</p>
              </div>
              <div class="icon"><i class="fas fa-tags"></i></div>
              <a href="{{ url('admin/category') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
              <div class="inner">
                <h3>{{ number_format($stats['assessments']) }}</h3>
                <p>Assessments</p>
              </div>
              <div class="icon"><i class="fas fa-clipboard-list"></i></div>
              <a href="{{ url('admin/courses') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-dark">
              <div class="inner">
                <h3>{{ number_format($stats['transactions']) }}</h3>
                <p>Transactions</p>
              </div>
              <div class="icon"><i class="fas fa-receipt"></i></div>
              <a href="{{ url('admin/repors') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color:#16a085;color:#fff;">
              <div class="inner">
                <h3 style="white-space:nowrap;">&#8377; {{ number_format($stats['revenue']) }}</h3>
                <p>Total Revenue</p>
              </div>
              <div class="icon"><i class="fas fa-rupee-sign"></i></div>
              <a href="{{ url('admin/repors') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <!-- Recent students -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-clock mr-2"></i>Recently Registered Students</h3>
                <div class="card-tools">
                  <a href="{{ url('admin/users') }}" class="btn btn-sm btn-primary">View all</a>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Mobile</th>
                      <th>Registered</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($recentUsers as $u)
                      <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->name ?: '—' }}</td>
                        <td>{{ $u->email ?: '—' }}</td>
                        <td>{{ $u->mobile_number ?: '—' }}</td>
                        <td>{{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d M Y') : '—' }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="5" class="text-center">No students yet.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection
