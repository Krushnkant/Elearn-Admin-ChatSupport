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
                                @php
                                  $isLinkVideo = preg_match('/^https?:\/\//i', $video_info->originalVideo ?? '');
                                @endphp
                                <div class="form-group col-md-12 ">
                                  <label>Video Source<span class="text-danger">*</span></label>
                                  <div>
                                    <label style="font-weight: normal; margin-right: 20px;">
                                      <input type="radio" name="video_source" value="file" {{ $isLinkVideo ? '' : 'checked' }} onclick="toggleVideoSource('file')"> Upload File
                                    </label>
                                    <label style="font-weight: normal;">
                                      <input type="radio" name="video_source" value="link" {{ $isLinkVideo ? 'checked' : '' }} onclick="toggleVideoSource('link')"> Paste Link
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 " id="videoFileGroup" style="{{ $isLinkVideo ? 'display:none;' : '' }}">
                                  <label for="video">Video File</label>
                                  <input type="file" name="video" class="form-control" id="video" accept="video/mp4,video/x-m4v,video/*">
                                  @if ($errors->has('video'))
                                  <p class="error text text-danger">
                                      <i class="fa fa-times-circle-o"></i>  {{ $errors->first('video') }}
                                  </p>
                                  @endif
                                </div>

                                <div class="form-group col-md-6 " id="videoUrlGroup" style="{{ $isLinkVideo ? '' : 'display:none;' }}">
                                  <label for="video_url">Video Link</label>
                                  <input type="text" name="video_url" class="form-control" id="video_url" placeholder="https://... (YouTube, Vimeo, or any direct video link)" value="{{ $isLinkVideo ? $video_info->originalVideo : old('video_url') }}">
                                  @if ($errors->has('video_url'))
                                  <p class="error text text-danger">
                                      <i class="fa fa-times-circle-o"></i>  {{ $errors->first('video_url') }}
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

                                <div class="form-group col-md-6 ">
                                  <label for="duration">Duration</label>
                                  <input type="text" name="duration" class="form-control" id="duration" placeholder="e.g. 12:34" value="{{ old('duration', $video_info->duration) }}" style="max-width: 200px;">
                                  <small class="form-text text-muted" id="durationHint" style="display:none;">Auto-filled from the video — edit it if it's not right.</small>
                                  @if ($errors->has('duration'))
                                  <p class="error text text-danger">
                                      <i class="fa fa-times-circle-o"></i>  {{ $errors->first('duration') }}
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

    function toggleVideoSource(mode) {
      var fileGroup = document.getElementById('videoFileGroup');
      var urlGroup  = document.getElementById('videoUrlGroup');
      var fileInput = document.getElementById('video');
      var urlInput  = document.getElementById('video_url');

      if (mode === 'link') {
        fileGroup.style.display = 'none';
        urlGroup.style.display = '';
        fileInput.value = '';
      } else {
        urlGroup.style.display = 'none';
        fileGroup.style.display = '';
        urlInput.value = '';
      }
    }

    // Auto-fill Duration from the real video length — see add.blade.php for
    // the full rationale (no server-side ffmpeg/getID3 dependency needed).
    function formatDuration(totalSeconds) {
      totalSeconds = Math.round(totalSeconds);
      var h = Math.floor(totalSeconds / 3600);
      var m = Math.floor((totalSeconds % 3600) / 60);
      var s = totalSeconds % 60;
      var pad = function(n) { return n < 10 ? '0' + n : '' + n; };
      return h > 0 ? (h + ':' + pad(m) + ':' + pad(s)) : (pad(m) + ':' + pad(s));
    }

    function readDurationFromSrc(src, revokeAfter) {
      var probe = document.createElement('video');
      probe.preload = 'metadata';
      probe.onloadedmetadata = function() {
        if (isFinite(probe.duration) && probe.duration > 0) {
          document.getElementById('duration').value = formatDuration(probe.duration);
          document.getElementById('durationHint').style.display = '';
        }
        if (revokeAfter) URL.revokeObjectURL(src);
      };
      probe.onerror = function() {
        if (revokeAfter) URL.revokeObjectURL(src);
      };
      probe.src = src;
    }

    document.getElementById('video').addEventListener('change', function(e) {
      var file = e.target.files && e.target.files[0];
      if (file) readDurationFromSrc(URL.createObjectURL(file), true);
    });

    // YouTube/Vimeo links: their own Player APIs know the real duration once
    // the video has loaded, even though there's no raw file to probe. Load a
    // throwaway off-screen player, ask it for the duration, then tear it down.
    function applyDetectedDuration(seconds) {
      if (isFinite(seconds) && seconds > 0) {
        document.getElementById('duration').value = formatDuration(seconds);
        document.getElementById('durationHint').style.display = '';
      }
    }

    function makeHiddenHost() {
      var host = document.createElement('div');
      host.style.cssText = 'position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden;';
      host.id = 'durationProbe' + Date.now();
      document.body.appendChild(host);
      return host;
    }

    var ytApiQueue = [];
    function loadYouTubeApi(cb) {
      if (window.YT && window.YT.Player) { cb(); return; }
      ytApiQueue.push(cb);
      if (!document.getElementById('yt-iframe-api')) {
        var s = document.createElement('script');
        s.id = 'yt-iframe-api'; s.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(s);
      }
    }
    var priorYTReady = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = function() {
      if (typeof priorYTReady === 'function') priorYTReady();
      var q = ytApiQueue; ytApiQueue = [];
      q.forEach(function(cb) { cb(); });
    };

    function probeYouTubeDuration(videoId) {
      loadYouTubeApi(function() {
        var host = makeHiddenHost();
        try {
          var player = new YT.Player(host.id, {
            videoId: videoId,
            events: {
              onReady: function(e) {
                applyDetectedDuration(e.target.getDuration());
                try { e.target.destroy(); } catch (err) {}
                host.remove();
              },
              onError: function() { host.remove(); }
            }
          });
        } catch (err) { host.remove(); }
      });
    }

    var vimeoApiQueue = [];
    function loadVimeoApi(cb) {
      if (window.Vimeo && window.Vimeo.Player) { cb(); return; }
      vimeoApiQueue.push(cb);
      if (!document.getElementById('vimeo-player-api')) {
        var s = document.createElement('script');
        s.id = 'vimeo-player-api'; s.src = 'https://player.vimeo.com/api/player.js';
        s.onload = function() { var q = vimeoApiQueue; vimeoApiQueue = []; q.forEach(function(cb) { cb(); }); };
        document.head.appendChild(s);
      }
    }

    function probeVimeoDuration(videoId) {
      loadVimeoApi(function() {
        var host = makeHiddenHost();
        var iframe = document.createElement('iframe');
        iframe.src = 'https://player.vimeo.com/video/' + videoId;
        iframe.width = 200; iframe.height = 113; iframe.frameBorder = 0;
        host.appendChild(iframe);
        try {
          var player = new Vimeo.Player(iframe);
          player.getDuration().then(function(seconds) {
            applyDetectedDuration(seconds);
            host.remove();
          }).catch(function() { host.remove(); });
        } catch (err) { host.remove(); }
      });
    }

    document.getElementById('video_url').addEventListener('blur', function(e) {
      var url = e.target.value.trim();
      if (!url) return;

      var yt = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]{6,})/i);
      if (yt) { probeYouTubeDuration(yt[1]); return; }

      var vim = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
      if (vim) { probeVimeoDuration(vim[1]); return; }

      if (/\.(mp4|webm|ogg|mov|m4v)(\?|$)/i.test(url)) {
        readDurationFromSrc(url, false);
      }
    });
</script>
@endsection