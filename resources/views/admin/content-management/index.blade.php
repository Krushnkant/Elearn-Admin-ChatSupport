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
            <div class="card-header bg-primary">
              <h3 class="card-title">Mock Test Page</h3>
            </div>
            <div class="box-body border border-primary">
              <form role="form" action="{{ url('admin/content-management/mock-test') }}" method="post">
                {{ csrf_field() }}
                <div class="box-body col-md-12">
                  <p class="text-muted mt-2">
                    Header shown at the top of the <strong>Mock Tests</strong> tab in the app.
                  </p>
                  <div class="row">

                    <div class="form-group col-md-12">
                      <label for="title">Title<span class="text-danger">*</span></label>
                      <input type="text" name="title" class="form-control" id="title" maxlength="100" value="{{ old('title', $mock_test['title'] ?? '') }}">
                      @if ($errors->has('title'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('title') }}</p>@endif
                    </div>

                    @php
                      $badgeLabels = [
                        1 => 'Badge 1 (blue, top-left)',
                        2 => 'Badge 2 (teal, top-right)',
                        3 => 'Badge 3 (orange, bottom-left)',
                        4 => 'Badge 4 (purple, bottom-right)',
                      ];
                    @endphp
                    @foreach($badgeLabels as $i => $label)
                      <div class="form-group col-md-6">
                        <label for="badge_{{ $i }}">{{ $label }}<span class="text-danger">*</span></label>
                        <input type="text" name="badge_{{ $i }}" class="form-control" id="badge_{{ $i }}" maxlength="40" value="{{ old('badge_' . $i, $mock_test['badge_' . $i] ?? '') }}">
                        @if ($errors->has('badge_' . $i))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('badge_' . $i) }}</p>@endif
                      </div>
                    @endforeach

                    <div class="form-group col-md-12">
                      <label for="description">Description<span class="text-danger">*</span></label>
                      <textarea name="description" class="form-control" id="description" rows="3" maxlength="500">{{ old('description', $mock_test['description'] ?? '') }}</textarea>
                      @if ($errors->has('description'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('description') }}</p>@endif
                    </div>

                  </div>
                  <div class="form-group col-md-12">
                    <button type="submit" class="btn btn-primary">Save</button>
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
