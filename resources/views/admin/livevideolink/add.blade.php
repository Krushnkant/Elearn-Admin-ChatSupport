@extends('admin.layouts.master')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>{{ $title }}</h1>
    <small>Day {{ $day->day_no }}: {{ $day->title }}</small>
  </section>
  <section class="content">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="card-header bg-primary"></div>
          <div class="box-body border border-primary">
            <form role="form" action="{{ url('admin/live-videos/'.$day->id.'/links/store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body col-md-12">
                <div class="row">

                  <div class="form-group col-md-9">
                    <label for="title">Video Title<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="e.g. What is PMP?" value="{{ old('title') }}">
                    @if ($errors->has('title'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('title') }}</p>@endif
                  </div>

                  <div class="form-group col-md-3">
                    <label for="sort_order">Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control" id="sort_order" placeholder="0" value="{{ old('sort_order', 0) }}">
                  </div>

                  <div class="form-group col-md-9">
                    <label for="video_url">Video Link (URL)<span class="text-danger">*</span></label>
                    <input type="text" name="video_url" class="form-control" id="video_url" placeholder="https://youtube.com/watch?v=..." value="{{ old('video_url') }}">
                  </div>

                  <div class="form-group col-md-3">
                    <label for="duration_mins">Duration (mins)</label>
                    <input type="number" min="0" name="duration_mins" class="form-control" id="duration_mins" placeholder="e.g. 20" value="{{ old('duration_mins', 0) }}">
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
                  <a href="{{ url('admin/live-videos/'.$day->id.'/links') }}" class="btn btn-danger">Cancel</a>
                  <button type="submit" class="btn btn-primary">Submit</button>
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
