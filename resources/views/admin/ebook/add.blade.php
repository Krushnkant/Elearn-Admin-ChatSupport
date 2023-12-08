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
                    <form role="form" name="category_form" id="category_form" action="{{ url('admin/ebooks/store') }}" method="post" enctype="multipart/form-data">
                       {{ csrf_field() }}
                       <div class="box-body col-md-12">
                          <div class="row">
                             <div class="form-group col-md-12 ">
                                <label for="title">Title<span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" id="title" placeholder="Enter title" value="{{ old('title') }}">
                                @if ($errors->has('title'))
                                <p class="error text text-danger">
                                   <i class="fa fa-times-circle-o"></i>  {{ $errors->first('title') }}
                                </p>
                                @endif
                             </div>

                             <div class="form-group col-md-6">
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" value="1 " id="recommendation" name="recommendation">
                                  <label class="form-check-label" for="recommendation">
                                    Recommendation 
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1 " id="top_paid" name="top_paid">
                                    <label class="form-check-label" for="top_paid">
                                      Top Paid 
                                    </label>
                                  </div>
                              </div>

                             <div class="form-group col-md-6">
                                    <label for="duration">E-Book<span class="text-danger">*</span></label>
                                    <br />
                                    <input type="file" 
                                      name="ebook" 
                                      id="ebook" 
                                      >
                                    @if ($errors->has('ebook'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('ebook') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="duration">Preview<span class="text-danger">*</span></label>
                                    <br />
                                    <input type="file" 
                                      name="image" 
                                      id="image" 
                                      accept="image/png, image/gif, image/jpeg">
                                    @if ($errors->has('image'))
                                    <p class="error text text-danger">
                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('image') }}
                                    </p>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="title">Price<span class="text-danger">*</span></label>
                                    <input 
                                      type="text"
                                      name="price"
                                      class="form-control"
                                      id="price"
                                      placeholder="Enter price"
                                      required 
                                      value="{{ old('price') }}">

                                    @if ($errors->has('price'))
                                      <p class="error text text-danger">
                                          <i class="fa fa-times-circle-o"></i> 
                                          {{ $errors->first('price') }}
                                      </p>
                                    @endif

                                </div>

                             <div class="form-group col-md-6 ">
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
                          <div class="form-group col-md-12 ">
                            <label for="description">Description<span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" id="description" placeholder="Enter description"></textarea>
                            @if ($errors->has('description'))
                            <p class="error text text-danger">
                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('description') }}
                            </p>
                            @endif
                          </div>
                          
                          <div class="form-group col-md-12">
                             <a href="{{ url('admin/ebooks') }}" class="btn btn-danger">Cancel</a>
                             <button id="btn-ebooks" type="submit" class="btn btn-primary">Submit</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');
    </script>
@endsection