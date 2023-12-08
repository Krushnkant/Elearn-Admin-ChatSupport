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
                    <form role="form" name="category_form" id="category_form" action="{{ url('admin/videos/update') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="box-body col-md-12">
                            <div class="row">
                                <div class="form-group col-md-12 ">
                                    <label for="title">Title<span class="text-danger">*</span></label>
                                    <input type="hidden" name="chapter_id" id="chapter_id" value="{{ $chapter_id }}">
                                    <input type="hidden" name="id" id="id" value="{{ $id }}">
                                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter title" value="{{$video_info->title}}">
                                    @if ($errors->has('title'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('title') }}
                                    </p>
                                    @endif
                                </div>
								<div class="form-group col-md-12 ">
								  <label for="title">Description<span class="text-danger">*</span></label>                      
								  <textarea type="text" name="description" class="form-control" id="title" placeholder="Enter Description">{{$video_info->description}}</textarea>
								  @if ($errors->has('description'))
								  <p class="error text text-danger">
									  <i class="fa fa-times-circle-o"></i>  {{ $errors->first('description') }}
								  </p>
								  @endif
								</div>
                                <div class="form-group col-md-6 ">
                                  <label for="video">Video<span class="text-danger">*</span></label>
                                  <input type="file" name="video" class="form-control" id="video" accept="video/mp4,video/x-m4v,video/*">
                                  @if ($errors->has('video'))
                                  <p class="error text text-danger">
                                      <i class="fa fa-times-circle-o"></i>  {{ $errors->first('video') }}
                                  </p>
                                  @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <video width="200" height="100" autoplay id="video1">
                                      <source src="{{ $video_info->video }}" type="video/mp4">
                                    </video>
                                </div>
                                <div class="form-group col-md-2">
                                    <a href="javascript:void(0);" onclick="playPause()">Play/Pause</a>
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
                                    <div class="col-md-6">
                                      <img src="{{ $video_info->image_thumb }}" alt="video thumbnail" width="100" />
                                    </div>
                                  </div>
                                </div>

                                <div class="form-group col-md-12 " style="margin-top:20px";>
                                    <label for="status">Status<span class="text-danger">*</span></label>
                                    <input type="checkbox" class="form-check-input ml-2" name="status" value="1" class="form-control" id="status" {{ ($video_info->status == 1) ? ('checked') : ('') }}>
                                    @if ($errors->has('status'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('status') }}
                                    </p>
                                    @endif
                                </div>
                              </div>
                              

                               <div class="form-group col-md-12">
                                    <a href="{{ url('admin/chapters/'.$chapter_id.'/videos') }}" class="btn btn-danger">Cancel</a>
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

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAhX4GLdqtMBWhIAWcFKPVZMVjXrV_2hDQ&libraries=places"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var myVideo = document.getElementById("video1"); 

    function playPause() { 
      if (myVideo.paused) 
        myVideo.play(); 
      else 
        myVideo.pause(); 
    } 
</script>
@endsection