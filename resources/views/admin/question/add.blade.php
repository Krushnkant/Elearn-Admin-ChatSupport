@extends('admin.layouts.master')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
      <h1>
        {{ $title }}
      </h1>
    </section>
    <section class="content">
        <div class="card-body">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary box-solid">
                        <div class="card-header bg-primary"></div>
                        <div class="box-body border border-primary">
                            <form role="form" name="add_question_form" id="add_question_form" action="{{ url('admin/questions/store') }}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="assessment_id" id="assessment_id" value="{{ $assessment_id }}">
                                {{ csrf_field() }}
                                <div class="box-body col-md-12">
                                 
                                    <div class="row">
                                    <div class="form-group col-md-6 ">
                                            <label for="set_type">Set type<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="set_type[]" id="set_type" multiple>
                                                <option value="">Select Set Type</option>
                                                <option value="set1">set1</option>
                                                <option value="set2">set2</option>
                                                <option value="set3">set3</option>
                                                <option value="set4">set4</option>
                                                <option value="set5">set5</option>
                                                <option value="set6">set6</option>
                                                <option value="set7">set7</option>
                                                <option value="set8">set8</option>
                                                <option value="set9">set9</option>
                                                <option value="set10">set10</option>
                                               
                                            </select>
                                            @if ($errors->has('approset_typeach_id'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('set_type') }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6 ">
                                            <label for="category_id">Category (Domain)<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="domain_id[]" id="domain_id" multiple>
                                                <option value="">Select Caegory</option>
                                                @if(!empty($category_info))
                                                    @foreach($category_info as $cat)
                                                        @if($cat->original_type == 1)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('category_id'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('category_id') }}
                                            </p>
                                            @endif
                                        </div>
                                        <!-- <input type="text" name="category_id" id="category_id" value="1"> -->
                                        <div class="form-group col-md-6 ">
                                            <label for="knowledge_id">Knowledge<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="knowledge_id[]" id="knowledge_id" multiple>
                                                <option value="">Select knowledge</option>
                                                @if(!empty($category_info))
                                                    @foreach($category_info as $cat)
                                                        @if($cat->original_type == 2)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('knowledge_id'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('knowledge_id') }}
                                            </p>
                                            @endif
                                        </div>
                                        <!-- <input type="text" name="category_id" id="category_id" value="2"> -->
                                        <div class="form-group col-md-6 ">
                                            <label for="approach_id">Approach<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="approach_id[]" id="approach_id" multiple>
                                                <option value="">Select approach</option>
                                                @if(!empty($category_info))
                                                    @foreach($category_info as $cat)
                                                        @if($cat->original_type == 3)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('approach_id'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('approach_id') }}
                                            </p>
                                            @endif
                                        </div>
                                        <!-- <input type="text" name="category_id" id="category_id" value="3"> -->
                                        <div class="form-group col-md-6 ">
                                            <label for="title">Marks<span class="text-danger">*</span></label>
                                            <input type="text" name="marks" class="form-control" id="marks" placeholder="Enter marks" value="">
                                            @if ($errors->has('marks'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('marks') }}
                                            </p>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-12 ">
                                            <label for="title">Title<span class="text-danger">*</span></label>
                                            <textarea name="title" class="form-control" id="title" placeholder="Enter title"></textarea>
                                            @if ($errors->has('title'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('title') }}
                                            </p>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-12 ">
                                            <label for="title">Explaination<span class="text-danger">*</span></label>
                                            <textarea name="explanation" class="form-control" id="explanation" placeholder="Enter explanation"></textarea>
                                            @if ($errors->has('explanation'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('explanation') }}
                                            </p>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-6 ">
                                            <label for="title">Difficulty Level<span class="text-danger">*</span></label>
                                            <input type="text" name="dificulty_level" class="form-control" id="dificulty_level" placeholder="Enter dificulty_level" value="{{ old('dificulty_level') }}">
                                            @if ($errors->has('dificulty_level'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('dificulty_level') }}
                                            </p>
                                            @endif
                                        </div>
                                         
                                        <!-- <div class="form-group col-md-6 " style="margin-top:20px";>
                                            <label for="question_type">Question Type<span class="text-danger">*</span></label><br>
                                            <input type="radio" id="single" name="question_type" value="1" checked>Single
                                            <input type="radio" id="multiple" name="question_type" value="2">Multiple <br>
                                            @if ($errors->has('question_type'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('question_type') }}
                                            </p>
                                            @endif
                                        </div> -->

                                        <div class="form-group col-md-6 ">
                                            <label for="status">Question Type<span class="text-danger">*</span></label>
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-secondary active">
                                                <input type="radio" name="question_type" id="single" autocomplete="off" value="1" checked> Single
                                                </label>
                                                <label class="btn btn-secondary">
                                                <input type="radio" name="question_type" id="multiple" autocomplete="off" value="2"> Multiple
                                                </label>
                                            </div>
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

                                    </div>

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 card">
                                            <h3>Question Options</h3>
                                            <div class="box-tools pull-right">
                                                <a href="javascript:void(0);" class="btn btn-warning add_field_button"> <i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Add Banners</a>
                                            </div>
                                            <div class="my-class">
                                                <div class="form-group col-md-12 ">
                                                    <label for="options">Option 1<span class="text-danger">*</span></label>
                                                    <input type="text" name="options[]" class="form-control" placeholder="Enter marks" value="">
                                                    @if ($errors->has('options.*'))
                                                    <p class="error text text-danger">
                                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('options.*') }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-12 ">
                                                    <label for="image">Image<span class="text-danger">*</span></label>
                                                    <input type="file" name="image[]" class="form-control">
                                                    @if ($errors->has('image'))
                                                    <p class="error text text-danger">
                                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('image') }}
                                                    </p>
                                                    @endif
                                                </div>
                                                

                                                <div class="form-group col-md-12 ">
                                                    <label for="is_correct">Is Correct<span class="text-danger">*</span></label>
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-secondary active">
                                                        <input type="radio" name="is_correct[0]" autocomplete="off" value="1" checked> Yes
                                                        </label>
                                                        <label class="btn btn-secondary">
                                                        <input type="radio" name="is_correct[0]" autocomplete="off" value="0"> No
                                                        </label>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                  

                                    <div class="form-group col-md-12">
                                        <a href="{{ url('admin/assessments/'.$assessment_id.'/questions') }}" class="btn btn-danger">Cancel</a>
                                        <button id="btn-category" type="submit" class="btn btn-primary" name="submit">Submit</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();

            var max_fields      = 4; //maximum input boxes allowed
            var wrapper         = $(".my-class"); //Fields wrapper
            var add_button      = $(".add_field_button"); //Add button ID
            
            var x = 0; //initlal text box count
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).append(`<hr><div class="form-group col-md-12 ">
                                                    <label for="options">Option ${x+1}<span class="text-danger">*</span></label>
                                                    <input type="text" name="options[]" class="form-control" placeholder="Enter marks" value="">
                                                    @if ($errors->has('options.*'))
                                                    <p class="error text text-danger">
                                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('options.*') }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-12 ">
                                                    <label for="image">Image<span class="text-danger">*</span></label>
                                                    <input type="file" name="image[]" class="form-control">
                                                    @if ($errors->has('image'))
                                                    <p class="error text text-danger">
                                                        <i class="fa fa-times-circle-o"></i>  {{ $errors->first('image') }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-12 ">
                                                    <label for="is_correct">Is Correct<span class="text-danger">*</span></label>
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-secondary ">
                                                        <input type="radio"  name="is_correct[${x}]" value="1"> Yes
                                                        </label>
                                                        <label class="btn btn-secondary active">
                                                        <input type="radio" checked name="is_correct[${x}]" value="0"> No
                                                        </label>
                                                    </div>
                                                </div>`); //add input box
                }
            });
            
            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })
        });  
    </script>
@endsection