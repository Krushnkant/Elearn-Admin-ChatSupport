@extends('admin.layouts.master')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>{{ $title }}</h1>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <b>{{ $enquiry->subject ?: 'General' }}</b>
              <div class="box-tools float-right">
                <a href="mailto:{{ $enquiry->email }}?subject=Re: {{ rawurlencode($enquiry->subject ?: 'Your enquiry') }}" class="btn btn-primary btn-sm"><i class="fa fa-reply"></i> Reply by Email</a>
                <a href="{{ url('admin/enquiries') }}" class="btn btn-default btn-sm">Back</a>
              </div>
            </div>
            <div class="card-body">
              <table class="table table-sm table-borderless" style="width:auto">
                <tr><th style="width:130px">From</th><td>{{ $enquiry->name }} &lt;{{ $enquiry->email }}&gt;</td></tr>
                @if($enquiry->user)
                <tr><th>Account</th><td>{{ $enquiry->user->name }} (user #{{ $enquiry->user->id }}, {{ $enquiry->user->email }})</td></tr>
                @endif
                <tr><th>Received</th><td>{{ $enquiry->created_at }}</td></tr>
                <tr><th>Email notification</th><td>{{ $enquiry->mailed ? 'Sent' : 'Not sent (saved here only)' }}</td></tr>
              </table>
              <hr>
              <p style="white-space: pre-wrap;">{{ $enquiry->message }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
