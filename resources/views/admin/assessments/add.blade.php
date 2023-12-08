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
                    <form role="form" name="category_form" id="category_form" action="{{ url('admin/assessments/store') }}" method="post" enctype="multipart/form-data">
                      {{ csrf_field() }}
                      <div class="box-body col-md-12">
                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="title">Title<span class="text-danger">*</span></label>
                            <input type="hidden" name="course_id" id="course_id" value="{{ $course_id }}">
                            <input type="text" name="title" class="form-control" id="title" placeholder="Enter title" value="{{ old('title') }}">
                            @if ($errors->has('title'))
                            <p class="error text text-danger">
                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('title') }}
                            </p>
                            @endif
                          </div>

                          <div class="form-group col-md-6">
                              <label for="category_id">Skill<span class="text-danger">*</span></label>
                              <select class="form-control" name="skill_id" id="skill_id">
                                  <option value="">Select skill</option>
                                  @if(!empty($skill_info))
                                      @foreach($skill_info as $skill)
                                          <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                      @endforeach
                                  @endif
                              </select>
                              @if ($errors->has('skill_id'))
                              <p class="error text text-danger">
                                  <i class="fa fa-times-circle-o"></i>  {{ $errors->first('skill_id') }}
                              </p>
                              @endif
                          </div>

                          <div class="form-group col-md-6">
                            <label for="title">Number of question<span class="text-danger">*</span></label>
                            <input type="number" name="number_of_questions" class="form-control" id="number_of_questions" placeholder="Enter number of question" value="{{ old('number_of_questions') }}" min="1">
                            @if ($errors->has('number_of_questions'))
                            <p class="error text text-danger">
                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('number_of_questions') }}
                            </p>
                            @endif
                          </div>

                          <div class="form-group col-md-6">
                            <label for="title">Mock Exam<span class="text-danger">*</span></label>
                            <input type="number" name="mock_exam" class="form-control" id="mock_exam" placeholder="Enter mock exam" value="{{ old('mock_exam') }}" min="1">
                            @if ($errors->has('mock_exam'))
                            <p class="error text text-danger">
                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('mock_exam') }}
                            </p>
                            @endif
                          </div>

                          <div class="form-group col-md-12">
                            <label for="title">Description<span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter description">{{ old('description') }}</textarea>
                            @if ($errors->has('description'))
                            <p class="error text text-danger">
                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('description') }}
                            </p>
                            @endif
                          </div>

                          <div class="form-group col-md-6">
                            <label for="status">Status<span class="text-danger">*</span></label>
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
                            <label for="duration">Image<span class="text-danger">*</span></label>
                            <input type="file" name="image" id="image">
                            @if ($errors->has('image'))
                            <p class="error text text-danger">
                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('image') }}
                            </p>
                            @endif
                          </div>
                        </div>

                        <div class="form-group col-md-12">
                          <a href="{{ url('admin/courses/'.$course_id.'/assessments') }}" class="btn btn-danger">Cancel</a>
                          <button id="btn-category" type="submit" class="btn btn-primary">Submit</button>
                        </div>
                      </div>    
                    </form> 
                  </div>
                </div>
           </div>
       </div>
     </div>
     </section>     
</div>

@endsection

@section('css')

@endsection
@section('js')
  <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
  <script>
      CKEDITOR.replace('description');
  </script>
@endsection