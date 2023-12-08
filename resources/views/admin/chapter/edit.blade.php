@extends('admin.layouts.master')

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
                    <form role="form" name="category_form" id="category_form" action="{{ url('admin/chapters/update') }}" method="post" >
                        {{ csrf_field() }}
                        <div class="box-body col-md-12">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="title">Chapter No<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" value="Chapter" disabled>
                                </div>
                                <div class="form-group col-md-3" style="margin-top: 33px;">
                                    <select class="form-control" name="chapter" id="chapter">
                                      <option value="">Please select chapter</option>
                                      @for ($i = 1; $i < 31; $i++)
                                          <option value="{{ $i }}" {{ ($chapter_info->originalChapter == $i) ? ('selected') : ('') }}>{{ $i }}</option>
                                      @endfor
                                      
                                    </select>
                                    @if ($errors->has('chapter'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('chapter') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6 ">
                                    <label for="title">Name<span class="text-danger">*</span></label>
                                    <input type="hidden" name="course_id" id="course_id" value="{{ $course_id }}">
                                    <input type="hidden" name="id" id="id" value="{{ $id }}">
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter name" value="{{$chapter_info->name}}">
                                    @if ($errors->has('name'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('name') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-12 ">
                                  <label for="status">Status<span class="text-danger">*</span></label>
                                  <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary active">
                                      <input type="radio" name="status" id="active" autocomplete="off" value="1" {{ ($chapter_info->status == 1) ? ('checked') : ('') }}> Active
                                    </label>
                                    <label class="btn btn-secondary">
                                      <input type="radio" name="status" id="inactive" autocomplete="off" value="0" {{ ($chapter_info->status == 0) ? ('checked') : ('') }}> Inactive
                                    </label>
                                  </div>
                                </div>
                              </div>
                              

                               <div class="form-group col-md-12">
                                    <a href="{{ url('admin/courses/'.$course_id.'/chapters') }}" class="btn btn-danger">Cancel</a>
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

@section('css')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAhX4GLdqtMBWhIAWcFKPVZMVjXrV_2hDQ&libraries=places"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script>
@endsection