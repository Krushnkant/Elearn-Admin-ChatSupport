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
            <div class="card-header bg-primary"></div>
            <div class="box-body border border-primary">
              <form role="form" name="chapter_videos_form" id="chapter_videos_form" action="{{ url('admin/videos/store') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="box-body col-md-12">
                  <div class="row">
                    <div class="form-group col-md-12 ">
                      <label for="title">Title<span class="text-danger">*</span></label>
                      <input type="hidden" name="chapter_id" id="chapter_id" value="{{ $chapter_id }}">
                      <input type="text" name="title" class="form-control" id="title" placeholder="Enter title" value="{{ old('title') }}">
                      @if ($errors->has('title'))
                      <p class="error text text-danger">
                          <i class="fa fa-times-circle-o"></i>  {{ $errors->first('title') }}
                      </p>
                      @endif
                    </div>
					
					<div class="form-group col-md-12 ">
                      <label for="title">Description<span class="text-danger">*</span></label>                      
                      <textarea type="text" name="description" class="form-control" id="title" placeholder="Enter Description">{{ old('description') }}</textarea>
                      @if ($errors->has('description'))
                      <p class="error text text-danger">
                          <i class="fa fa-times-circle-o"></i>  {{ $errors->first('description') }}
                      </p>
                      @endif
                    </div>

                    <div class="form-group col-md-12 ">
                      <label for="video">Video<span class="text-danger">*</span></label>
                      <input type="file" name="video" class="form-control" id="video"  accept="video/mp4,video/x-m4v,video/*">
                      @if ($errors->has('video'))
                      <p class="error text text-danger">
                          <i class="fa fa-times-circle-o"></i>  {{ $errors->first('video') }}
                      </p>
                      @endif
                    </div>

                    

                    <div class="form-group col-md-12">
                      <div class="row">
                        <div class="col-md-6">
                          <label for="video">Thumbnail<span class="text-danger">*</span></label>
                          <input 
                            type="file"
                            name="thumbnail"
                            class="form-control"
                            id="thumbnail"
                            accept="image/png, image/gif, image/jpeg">
                              @if ($errors->has('thumbnail'))
                              <p class="error text text-danger">
                                  <i class="fa fa-times-circle-o"></i>
                                  {{ $errors->first('thumbnail') }}
                              </p>
                              @endif
                        </div>
                      </div>
                    </div>
              
                    <div class="form-group col-md-12 " style="margin-top:20px";>
                      <label for="status">Status<span class="text-danger">*</span></label>
                      <input type="checkbox" class="form-check-input ml-2" name="status" value="1" class="form-control" id="status">
                      @if ($errors->has('status'))
                      <p class="error text text-danger">
                          <i class="fa fa-times-circle-o"></i>  {{ $errors->first('status') }}
                      </p>
                      @endif
                    </div>
                  </div>
                      
                  <div class="form-group col-md-12">
                    <a href="{{ url('admin/courses/'.$chapter_id.'/chapters') }}" class="btn btn-danger">Cancel</a>
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

@endsection