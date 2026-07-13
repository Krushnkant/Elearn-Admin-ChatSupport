@extends('admin.layouts.master')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>{{ $title }}</h1>
    <small>{{ $book->title }}</small>
  </section>
  <section class="content">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="card-header bg-primary"></div>
          <div class="box-body border border-primary">
            <form role="form" action="{{ url('admin/study-materials/'.$book->id.'/lessons/store') }}" method="post" enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="box-body col-md-12">
                <div class="row">

                  <div class="form-group col-md-9">
                    <label for="title">Lesson Title<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="e.g. Learner Handout - Lesson 1" value="{{ old('title') }}">
                    @if ($errors->has('title'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('title') }}</p>@endif
                  </div>

                  <div class="form-group col-md-3">
                    <label for="sort_order">Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control" id="sort_order" placeholder="0" value="{{ old('sort_order', 0) }}">
                  </div>

                  <div class="form-group col-md-12">
                    <label for="file">PDF File <small class="text-muted">(optional — leave blank to use text content below)</small></label>
                    <br /><input type="file" name="file" id="file" accept="application/pdf">
                  </div>

                  <div class="form-group col-md-12">
                    <label for="content">Content <small class="text-muted">(rich text — used when no PDF is uploaded)</small></label>
                    <textarea name="content" class="form-control" id="content">{{ old('content') }}</textarea>
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
                  <a href="{{ url('admin/study-materials/'.$book->id.'/lessons') }}" class="btn btn-danger">Cancel</a>
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
@section('js')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('content');</script>
@endsection
