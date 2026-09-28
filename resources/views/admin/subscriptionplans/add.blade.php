@extends('admin.layouts.master')
@php
  $themeOptions = ['free' => 'Free', 'basic' => 'Basic', 'pro' => 'Professional', 'prem' => 'Premium', 'elite' => 'Elite'];
  $iconOptions = ['bi-tree-fill','bi-mortarboard-fill','bi-trophy-fill','bi-gem','bi-shield-fill-check','bi-star-fill','bi-rocket-takeoff-fill','bi-award-fill','bi-lightning-fill','bi-diamond-fill'];
  $badgeTypeOptions = ['' => 'None', 'popular' => 'Popular (highlighted card)', 'limited' => 'Limited (dark badge)'];
  $featureRows = old('features', [['text' => '', 'enabled' => true]]);
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
            <form role="form" action="{{ url('admin/subscription-plans/store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body col-md-12">
                <div class="row">

                  <div class="form-group col-md-9">
                    <label for="name">Name<span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="e.g. Professional Membership" value="{{ old('name') }}">
                    @if ($errors->has('name'))<p class="error text text-danger"><i class="fa fa-times-circle-o"></i> {{ $errors->first('name') }}</p>@endif
                  </div>

                  <div class="form-group col-md-3">
                    <label for="sort_order">Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control" id="sort_order" placeholder="0" value="{{ old('sort_order', 0) }}">
                  </div>

                  <div class="form-group col-md-4">
                    <label for="theme">Theme</label>
                    <select name="theme" id="theme" class="form-control">
                      @foreach($themeOptions as $val => $label)
                        <option value="{{ $val }}" {{ old('theme') === $val ? 'selected' : '' }}>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="icon">Icon</label>
                    <select name="icon" id="icon" class="form-control">
                      @foreach($iconOptions as $ic)
                        <option value="{{ $ic }}" {{ old('icon') === $ic ? 'selected' : '' }}>{{ $ic }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="badge_type">Badge</label>
                    <select name="badge_type" id="badge_type" class="form-control">
                      @foreach($badgeTypeOptions as $val => $label)
                        <option value="{{ $val }}" {{ old('badge_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="annual_price">Annual Price (₹)</label>
                    <input type="number" min="0" name="annual_price" class="form-control" id="annual_price" value="{{ old('annual_price', 0) }}">
                  </div>

                  <div class="form-group col-md-4">
                    <label for="annual_old_price">Annual "Before" Price (₹)</label>
                    <input type="number" min="0" name="annual_old_price" class="form-control" id="annual_old_price" value="{{ old('annual_old_price', 0) }}">
                  </div>

                  <div class="form-group col-md-4">
                    <label for="monthly_price">Monthly Price (₹)</label>
                    <input type="number" min="0" name="monthly_price" class="form-control" id="monthly_price" value="{{ old('monthly_price', 0) }}">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="ideal_text">"Ideal for" Text</label>
                    <input type="text" name="ideal_text" class="form-control" id="ideal_text" placeholder="e.g. Ideal for Working Professionals" value="{{ old('ideal_text') }}">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="badge_text">Badge Text</label>
                    <input type="text" name="badge_text" class="form-control" id="badge_text" placeholder="e.g. Most Popular" value="{{ old('badge_text') }}">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="cta_text">Button Text</label>
                    <input type="text" name="cta_text" class="form-control" id="cta_text" placeholder="e.g. Choose Professional" value="{{ old('cta_text', 'Choose Plan') }}">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="inherit_text">"Inherits from" Text</label>
                    <input type="text" name="inherit_text" class="form-control" id="inherit_text" placeholder="e.g. Everything in BASIC +" value="{{ old('inherit_text') }}">
                  </div>

                  <div class="form-group col-md-12">
                    <label>Features</label>
                    <table class="table table-sm table-bordered" id="featuresTable">
                      <thead>
                        <tr><th>Feature</th><th style="width:100px;">Included</th><th style="width:60px;"></th></tr>
                      </thead>
                      <tbody id="featuresBody">
                        @foreach($featureRows as $i => $row)
                        <tr>
                          <td><input type="text" name="features[{{ $i }}][text]" class="form-control" value="{{ $row['text'] ?? '' }}" placeholder="Feature text"></td>
                          <td class="text-center">
                            <input type="hidden" name="features[{{ $i }}][enabled]" value="0">
                            <input type="checkbox" name="features[{{ $i }}][enabled]" value="1" {{ !empty($row['enabled']) ? 'checked' : '' }}>
                          </td>
                          <td><button type="button" class="btn btn-danger btn-sm remove-feature-row">&times;</button></td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <button type="button" id="addFeatureRow" class="btn btn-secondary btn-sm">+ Add Feature</button>
                    <p class="text-muted" style="margin-top:6px;">Uncheck "Included" to show a feature crossed-out (not included in this plan) instead of removing it.</p>
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
                  <a href="{{ url('admin/subscription-plans') }}" class="btn btn-danger">Cancel</a>
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
<script type="text/javascript">
  (function () {
    var body = document.getElementById('featuresBody');
    var nextIndex = {{ count($featureRows) }};

    document.getElementById('addFeatureRow').addEventListener('click', function () {
      var tr = document.createElement('tr');
      tr.innerHTML =
        '<td><input type="text" name="features[' + nextIndex + '][text]" class="form-control" placeholder="Feature text"></td>' +
        '<td class="text-center">' +
          '<input type="hidden" name="features[' + nextIndex + '][enabled]" value="0">' +
          '<input type="checkbox" name="features[' + nextIndex + '][enabled]" value="1" checked>' +
        '</td>' +
        '<td><button type="button" class="btn btn-danger btn-sm remove-feature-row">&times;</button></td>';
      body.appendChild(tr);
      nextIndex++;
    });

    body.addEventListener('click', function (e) {
      if (e.target.classList.contains('remove-feature-row')) {
        e.target.closest('tr').remove();
      }
    });
  })();
</script>
@endsection
