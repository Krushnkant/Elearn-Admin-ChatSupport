@extends('admin.layouts.master')
@php
  $iconOptions = ['bi-journal-bookmark-fill','bi-person-workspace','bi-diagram-3','bi-mortarboard','bi-clipboard-data','bi-people','bi-shield-exclamation','bi-book','bi-book-half','bi-file-earmark-text','bi-lightbulb','bi-graph-up','bi-cash-coin','bi-patch-check','bi-chat-dots','bi-calculator'];
  $colorOptions = ['ic-blue','ic-green','ic-purple','ic-amber','ic-pink','ic-teal','ic-orange','ic-slate','ic-yellow'];
@endphp
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
            <form role="form" action="{{ url('admin/study-materials/store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body col-md-12">
                <div class="row">

                  <div class="form-group col-md-9">
                    <label for="title">Title<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="e.g. PMBOK® Guide 7th Edition" value="{{ old('title') }}">
                    @if ($errors->has('title'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('title') }}</p>@endif
                  </div>

                  <div class="form-group col-md-3">
                    <label for="sort_order">Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control" id="sort_order" placeholder="0" value="{{ old('sort_order', 0) }}">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="icon">Icon</label>
                    <select name="icon" id="icon" class="form-control">
                      @foreach($iconOptions as $ic)
                        <option value="{{ $ic }}" {{ old('icon') === $ic ? 'selected' : '' }}>{{ $ic }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="color">Color</label>
                    <select name="color" id="color" class="form-control">
                      @foreach($colorOptions as $c)
                        <option value="{{ $c }}" {{ old('color') === $c ? 'selected' : '' }}>{{ $c }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" placeholder="Short description">{{ old('description') }}</textarea>
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
                  <a href="{{ url('admin/study-materials') }}" class="btn btn-danger">Cancel</a>
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
