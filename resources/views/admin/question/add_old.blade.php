@extends('admin.layouts.master')

  
@section('content')
  
  <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1>General Form</h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="#">Home</a></li>
                           <li class="breadcrumb-item active">General Form</li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="container-fluid">
                  <div class="row">
                     <!-- left column -->
                     <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                           <div class="card-header">
                              <h3 class="card-title">Quick Example</h3>
                           </div>
                           <!-- /.card-header -->
                           <!-- form start -->
                           <form role="form" name="add_question_form" id="add_question_form" action="{{ url('admin/questions/store') }}" method="post" enctype="multipart/form-data">
                              {{ csrf_field() }}
                              <div class="card-body">
                                 <div class="col-sm-12">
                                    <div class="form-group">
                                       <label for="exampleInputEmail1">Upload Csv</label>
                                       <input type="file" class="form-control" name="question_csv" id="question_csv">
                                    </div>
                                 </div>
                              </div>
                              <div class="card-footer">
                                 <button type="submit" class="btn btn-primary">Submit</button>
                              </div>
                           </form>
                        </div>
                        <!-- /.card -->
                     </div>
                  </div>
               </div>
            </section>
         </div>
@endsection

@section('js')

@endsection