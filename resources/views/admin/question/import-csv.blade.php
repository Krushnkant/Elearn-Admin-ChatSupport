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
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
                <div class="card-header bg-primary"></div>
                <div class="box-body border border-primary">
                    <form role="form" name="add_question_form" id="add_question_form" action="{{ url('admin/questions/import-csv') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="assessment_id" id="assessment_id" value="{{ $assessment_id }}">
                        <div class="box-body col-md-12">
                            <div class="row">
                                <div class="form-group col-md-12 ">
                                    <label for="question_csv">Import CSV</label>
                                    <input type="file" class="form-control" name="question_csv" id="question_csv">
                                    @if ($errors->has('question_csv'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('question_csv') }}
                                    </p>
                                    @endif
                                </div>
                              </div>

                               <div class="form-group col-md-12">
                                    <a href="{{ url('admin/questions') }}" class="btn btn-danger">Cancel</a>
                                    <button id="btn-category" type="submit" class="btn btn-primary">Submit</button>
                                </div>    
                         </form> 
                    </div>
               </div>
              
              <p style="margin:10px;"> <a href="{{url('import-csv')}}" >Download sample excel sheet</a></p>
              
           </div>
       </div>
     </div>
     </section>     
</div>

@endsection

@section('js')

@endsection