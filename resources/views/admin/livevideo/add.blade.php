@extends('admin.layouts.master')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>{{ $title }}</h1>
  </section>
  <section class="content">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="card-header bg-primary"></div>
          <div class="box-body border border-primary">
            <form role="form" name="livevideo_form" id="livevideo_form" action="{{ url('admin/live-videos/store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body col-md-12">
                <div class="row">

                  <div class="form-group col-md-3">
                    <label for="day_no">Day No<span class="text-danger">*</span></label>
                    <input type="number" min="1" name="day_no" class="form-control" id="day_no" placeholder="e.g. 1" value="{{ old('day_no') }}">
                    @if ($errors->has('day_no'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('day_no') }}</p>@endif
                  </div>

                  <div class="form-group col-md-9">
                    <label for="title">Title<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter session title" value="{{ old('title') }}">
                    @if ($errors->has('title'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('title') }}</p>@endif
                  </div>

                  <div class="form-group col-md-3">
                    <label for="video_count">Number of Videos</label>
                    <input type="number" min="0" name="video_count" class="form-control" id="video_count" placeholder="e.g. 6" value="{{ old('video_count', 0) }}">
                  </div>

                  <div class="form-group col-md-3">
                    <label for="duration_mins">Duration (mins)</label>
                    <input type="number" min="0" name="duration_mins" class="form-control" id="duration_mins" placeholder="e.g. 120" value="{{ old('duration_mins', 0) }}">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="scheduled_at">Scheduled At (optional live time)</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" id="scheduled_at" value="{{ old('scheduled_at') }}">
                  </div>

                  <div class="form-group col-md-12">
                    <label for="video_url">Video / Join Link (URL)</label>
                    <input type="text" name="video_url" class="form-control" id="video_url" placeholder="https://youtube.com/... or Zoom join link" value="{{ old('video_url') }}">
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" placeholder="Enter description">{{ old('description') }}</textarea>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="status">Status<span class="text-danger">*</span></label>
                    <br />
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                      <label class="btn btn-secondary active">
                        <input type="radio" name="status" id="active" autocomplete="off" value="1" checked> Active
                      </label>
                      <label class="btn btn-secondary">
                        <input type="radio" name="status" id="inactive" autocomplete="off" value="0"> Inactive
                      </label>
                    </div>
                  </div>

                </div>
                <div class="form-group col-md-12">
                  <a href="{{ url('admin/live-videos') }}" class="btn btn-danger">Cancel</a>
                  <button id="btn-livevideos" type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
