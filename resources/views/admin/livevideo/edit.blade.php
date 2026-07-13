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
          <div class="box box-primary box-solid">
            <div class="card-header bg-primary"></div>
            <div class="box-body border border-primary">
              <form role="form" name="livevideo_form" id="livevideo_form" action="{{ url('admin/live-videos/update') }}" method="post">
                {!! Form::hidden('id', $live_info->id, ['class' => 'form-control']) !!}
                {{ csrf_field() }}
                <div class="box-body col-md-12">
                  <div class="row">

                    <div class="form-group col-md-3">
                      <label for="day_no">Day No<span class="text-danger">*</span></label>
                      <input type="number" min="1" name="day_no" class="form-control" id="day_no" value="{{ old('day_no', $live_info->day_no) }}">
                      @if ($errors->has('day_no'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('day_no') }}</p>@endif
                    </div>

                    <div class="form-group col-md-9">
                      <label for="title">Title<span class="text-danger">*</span></label>
                      <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $live_info->title) }}">
                      @if ($errors->has('title'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('title') }}</p>@endif
                    </div>

                    <div class="form-group col-md-3">
                      <label for="video_count">Number of Videos</label>
                      <input type="number" min="0" name="video_count" class="form-control" id="video_count" value="{{ old('video_count', $live_info->video_count) }}">
                    </div>

                    <div class="form-group col-md-3">
                      <label for="duration_mins">Duration (mins)</label>
                      <input type="number" min="0" name="duration_mins" class="form-control" id="duration_mins" value="{{ old('duration_mins', $live_info->duration_mins) }}">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="scheduled_at">Scheduled At (optional live time)</label>
                      <input type="datetime-local" name="scheduled_at" class="form-control" id="scheduled_at"
                             value="{{ old('scheduled_at', $live_info->scheduled_at ? date('Y-m-d\TH:i', strtotime($live_info->scheduled_at)) : '') }}">
                    </div>

                    <div class="form-group col-md-12">
                      <label for="video_url">Video / Join Link (URL)</label>
                      <input type="text" name="video_url" class="form-control" id="video_url" value="{{ old('video_url', $live_info->video_url) }}">
                    </div>

                    <div class="form-group col-md-12">
                      <label for="description">Description</label>
                      <textarea name="description" class="form-control" id="description">{{ old('description', $live_info->description) }}</textarea>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="status">Status<span class="text-danger">*</span></label>
                      <br />
                      <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-secondary active">
                          <input type="radio" name="status" id="active" autocomplete="off" value="1" {{ ($live_info->status == 1) ? 'checked' : '' }}> Active
                        </label>
                        <label class="btn btn-secondary">
                          <input type="radio" name="status" id="inactive" autocomplete="off" value="0" {{ ($live_info->status == 0) ? 'checked' : '' }}> Inactive
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
    </div>
  </section>
</div>
@endsection
