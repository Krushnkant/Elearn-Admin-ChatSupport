@extends('admin.layouts.master')

@section('content')
   <div class="content-wrapper">
    <section class="content-header">
      <h1>
        {{ $title }}
      </h1>
    </section>
    <section class="content">
    <div class="card-body ">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
                <div class="card-header bg-primary"></div>
                <div class="box-body border border-primary">
                    <form role="form" name="category_form" id="category_form" action="{{ url('admin/category/store') }}" method="post" >
                        {{ csrf_field() }}
                        <div class="box-body col-md-12">
                         
                            <div class="row">
                                <div class="form-group col-md-12 ">
                                    <label for="title">Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter name" value="{{ old('name') }}">
                                    @if ($errors->has('name'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('name') }}
                                    </p>
                                    @endif
                                </div>
                        
                                <div class="form-group col-md-6 ">
                                <label for="status">Status<span class="text-danger">*</span></label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                   <label class="btn btn-secondary active">
                                   <input type="radio" name="status" id="active" autocomplete="off" value="1" checked> Active
                                   </label>
                                   <label class="btn btn-secondary">
                                   <input type="radio" name="status" id="inactive" autocomplete="off" value="0"> Inactive
                                   </label>
                                </div>
                             </div>

                                <div class="form-group col-md-12">
                                  <label for="status">Type<span class="text-danger">*</span></label>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="category" value="1">
                                    <label class="form-check-label" for="category">Category (Domain)</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="knowledge" value="2">
                                    <label class="form-check-label" for="knowledge">Knowledge</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="approach" value="3">
                                    <label class="form-check-label" for="approach">Approach</label>
                                  </div>
                                </div>
                              </div>
                              

                               <div class="form-group col-md-12">
                                    <a href="{{ url('admin/category') }}" class="btn btn-danger">Cancel</a>
                                    <button id="btn-category" type="submit" class="btn btn-primary">Submit</button>
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

@endsection
@section('js')

@endsection