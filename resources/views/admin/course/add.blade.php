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
                    <form role="form" name="add_course_form" id="add_course_form" action="{{ url('admin/courses/store') }}" method="post" enctype="multipart/form-data">
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
                                    <label for="mock_exam_count">Mock Exam Count<span class="text-danger">*</span></label>
                                    <input type="number" name="mock_exam_count" class="form-control" id="mock_exam_count" placeholder="Enter mock_exam_count" min="1">
                                    @if ($errors->has('mock_exam_count'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('mock_exam_count') }}
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
                                    <label for="duration">Duration<span class="text-danger">*</span></label>
                                    <input type="text" name="duration" class="form-control" id="duration" placeholder="Enter duration">
                                    @if ($errors->has('duration'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('duration') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6" style="margin-top:20px";>
                                    <!-- <label for="trainnig_mode">Trainning Mode<span class="text-danger">*</span></label>Flexible
                                    <input type="radio" class="form-check-input ml-2" name="trainnig_mode" value="1" class="form-control" id="trainnig_mode"> -->
                                     <label for="trainnig_mode">Trainning Mode<span class="text-danger">*</span></label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="trainnig_mode" id="flexible" value="1" checked>
                                        <label class="form-check-label" for="inlineRadio1">Flexible</label>
                                    </div>
                                    @if ($errors->has('trainnig_mode'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('trainnig_mode') }}
                                    </p>
                                    @endif
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

                                <div class="form-group col-md-12">
                                    <label for="overview">Course Overivew<span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="overview" id="overview"></textarea>
                                    @if ($errors->has('overview'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('overview') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="mock_test">Mock Test<span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="mock_test" id="mock_test"></textarea>
                                    @if ($errors->has('mock_test'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('mock_test') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="course_outline">Course Outline<span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="course_outline" id="course_outline"></textarea>
                                    @if ($errors->has('course_outline'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('course_outline') }}
                                    </p>
                                    @endif
                                </div>
                              </div>

                               <div class="form-group col-md-12">
                                    <a href="{{ url('admin/courses') }}" class="btn btn-danger">Cancel</a>
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

@endsection


@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('overview');
        CKEDITOR.replace('mock_test');
        CKEDITOR.replace('course_outline');
    </script>    
@endsection