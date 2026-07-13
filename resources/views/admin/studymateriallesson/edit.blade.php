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
          <div class="box box-primary box-solid">
            <div class="card-header bg-primary"></div>
            <div class="box-body border border-primary">
              <form role="form" action="{{ url('admin/study-materials/'.$book->id.'/lessons/update') }}" method="post" enctype="multipart/form-data">
                {!! Form::hidden('id', $lesson_info->id, ['class' => 'form-control']) !!}
                {{ csrf_field() }}
                <div class="box-body col-md-12">
                  <div class="row">

                    <div class="form-group col-md-9">
                      <label for="title">Lesson Title<span class="text-danger">*</span></label>
                      <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $lesson_info->title) }}">
                      @if ($errors->has('title'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('title') }}</p>@endif
                    </div>

                    <div class="form-group col-md-3">
                      <label for="sort_order">Order</label>
                      <input type="number" min="0" name="sort_order" class="form-control" id="sort_order" value="{{ old('sort_order', $lesson_info->sort_order) }}">
                    </div>

                    <div class="form-group col-md-12">
                      <label for="file">PDF File <small class="text-muted">(optional — upload to replace)</small></label>
                      @if($lesson_info->file)
                        <div class="mb-2">
                          <a href="{{ url('public/study_materials/'.$lesson_info->file) }}" target="_blank"><i class="fa fa-file-pdf"></i> Current PDF</a>
                          <label class="ml-3 text-danger" style="cursor:pointer;">
                            <input type="checkbox" name="remove_file" value="1"> Remove PDF (use text content instead)
                          </label>
                        </div>
                      @endif
                      <input type="file" name="file" id="file" accept="application/pdf">
                    </div>

                    <div class="form-group col-md-12">
                      <label for="content">Content <small class="text-muted">(rich text — used when no PDF)</small></label>
                      <textarea name="content" class="form-control" id="content">{{ old('content', $lesson_info->content) }}</textarea>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="status">Status<span class="text-danger">*</span></label>
                      <br />
                      <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-secondary active">
                          <input type="radio" name="status" id="active" autocomplete="off" value="1" {{ ($lesson_info->status == 1) ? 'checked' : '' }}> Active
                        </label>
                        <label class="btn btn-secondary">
                          <input type="radio" name="status" id="inactive" autocomplete="off" value="0" {{ ($lesson_info->status == 0) ? 'checked' : '' }}> Inactive
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
    </div>
  </section>
</div>
@endsection
@section('js')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('content');</script>
@endsection
