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
                            <form role="form" name="edit_question_form" id="edit_question_form" action="{{ url('admin/questions/update') }}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                {!!Form::hidden('id',$question_info->id, ['class' => 'form-control'])!!}
                                <input type="hidden" name="assessment_id" id="assessment_id" value="{{ $assessment_id }}">
                                <div class="box-body col-md-12">
                                 
                                    <div class="row">
                                        <div class="form-group col-md-6 ">
                                            <label for="category_id">Category<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="domain_id[]" id="domain_id" multiple>
                                                <option value="">Select Category</option>
                                                @if(!empty($category_info))
                                                    @foreach($category_info as $cat)
                                                        @if($cat->original_type == 1)
                                                            <option value="{{ $cat->id }}" {{ $cat->id == $question_info->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
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

                                        <div class="form-group col-md-6 ">
                                            <label for="knowledge_id">Knowledge<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="knowledge_id[]" id="knowledge_id" multiple>
                                                <option value="">Select knowledge</option>
                                                <?php
                                                    $knowledge_arr = [];
                                                    if(!empty($question_info->categoryQuestion)) {
                                                        foreach($question_info->categoryQuestion as $cat_ques) {
                                                            $knowledge_arr[] = $cat_ques->category_id;
                                                        }
                                                    }
                                                ?>
                                                @if(!empty($category_info))
                                                    @foreach($category_info as $cat)
                                                        @if($cat->original_type == 2)
                                                            <option value="{{ $cat->id }}" {{ in_array($cat->id, $knowledge_arr) ? 'selected' : '' }}>{{ $cat->name }}</option>
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

                                        <div class="form-group col-md-6 ">
                                            <label for="approach_id">Approach<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="approach_id[]" id="approach_id" multiple>
                                                <option value="">Select approach</option>
                                                <?php
                                                    $approach_arr = [];
                                                    if(!empty($question_info->categoryQuestion)) {
                                                        foreach($question_info->categoryQuestion as $cat_ques) {
                                                            $approach_arr[] = $cat_ques->category_id;
                                                        }
                                                    }
                                                ?>
                                                @if(!empty($category_info))
                                                    @foreach($category_info as $cat)
                                                        @if($cat->original_type == 3)
                                                            <option value="{{ $cat->id }}" {{ in_array($cat->id, $approach_arr) ? 'selected' : '' }}>{{ $cat->name }}</option>
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

                                        <div class="form-group col-md-12 ">
                                            <label for="title">Title<span class="text-danger">*</span></label>
                                            <textarea name="title" class="form-control" id="title" placeholder="Enter title">{{ (!empty($question_info->title)) ? (trim($question_info->title)) : ('') }}</textarea>
                                            @if ($errors->has('title'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('title') }}
                                            </p>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-12 ">
                                            <label for="title">Explaination<span class="text-danger">*</span></label>
                                            <textarea name="explanation" class="form-control" id="explanation" placeholder="Enter explanation">{{ (!empty($question_info->explanation)) ? (trim($question_info->explanation)) : ('') }}</textarea>
                                            @if ($errors->has('explanation'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('explanation') }}
                                            </p>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-6 ">
                                            <label for="title">Difficulty Level<span class="text-danger">*</span></label>
                                            <input type="text" name="dificulty_level" class="form-control" id="dificulty_level" placeholder="Enter dificulty_level" value="{{$question_info->dificulty_level}}">
                                            @if ($errors->has('dificulty_level'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('dificulty_level') }}
                                            </p>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-6 ">
                                            <label for="title">Marks<span class="text-danger">*</span></label>
                                            <input type="number" name="marks" class="form-control" id="marks" placeholder="Enter marks" value="{{$question_info->marks}}">
                                            @if ($errors->has('marks'))
                                            <p class="error text text-danger">
                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('marks') }}
                                            </p>
                                            @endif
                                        </div>
                                         
                                        <!-- <div class="form-group col-md-12 " style="margin-top:20px";>
                                            <label for="question_type">Question Type<span class="text-danger">*</span></label><br>
                                            <input type="radio" id="single" name="question_type" value="1" {{ ($question_info->question_type == "Single") ? ('checked') : ('') }}>Single
                                            <input type="radio" id="multiple" name="question_type" value="2" {{ ($question_info->question_type == "Multiple") ? ('checked') : ('') }}>Multiple <br>
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
                                                <input type="radio" name="question_type" id="single" autocomplete="off" value="1" {{ ($question_info->question_type == "Single") ? ('checked') : ('') }}> Single
                                                </label>
                                                <label class="btn btn-secondary">
                                                <input type="radio" name="question_type" id="multiple" autocomplete="off" value="2" {{ ($question_info->question_type == "Multiple") ? ('checked') : ('') }}> Multiple
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6 ">
                                            <label for="status">Status<span class="text-danger">*</span></label>
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-secondary active">
                                                    <input type="radio" name="status" id="active" autocomplete="off" value="1" {{ ($question_info->status == 1) ? ('checked') : ('') }}> Active
                                                </label>
                                                <label class="btn btn-secondary">
                                                    <input type="radio" name="status" id="inactive" autocomplete="off" value="0" {{ ($question_info->status == 0) ? ('checked') : ('') }}> Inactive
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
                                                @if($question_info->questionOptions)
                                                    <input type="hidden" name="total_options" id="total_options" value="{{ count($question_info->questionOptions) }}">
                                                    @foreach($question_info->questionOptions as $key => $q_options)
                                                    <div class="shadow-lg p-3 mb-5 bg-white rounded" id="question-options-{{ $q_options->id }}">
                                                        <div class="form-group col-md-12 ">
                                                            <label for="options">Option {{ $key+1 }}<span class="text-danger">*</span></label>
                                                            <input type="hidden" name="options_id[]" id="options_id-{{ $q_options->id }}" value="{{ $q_options->id }}">
                                                            <input type="text" name="options[]" class="form-control" placeholder="Enter marks" value="{{ (!empty($q_options->options)) ? (trim($q_options->options)) : ('') }}">
                                                            @if ($errors->has('options'))
                                                            <p class="error text text-danger">
                                                                <i class="fa fa-times-circle-o"></i>  {{ $errors->first('options') }}
                                                            </p>
                                                            @endif
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group col-md-6 ">
                                                                <label for="image">Image {{ $key+1 }}<span class="text-danger">*</span></label>
                                                                <input type="file" name="image[]" class="form-control">
                                                                @if ($errors->has('image'))
                                                                <p class="error text text-danger">
                                                                    <i class="fa fa-times-circle-o"></i>  {{ $errors->first('image') }}
                                                                </p>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-6">
                                                                <img src="{{ $q_options->image }}" class="rounded" height="100px" width="150px;">
                                                            </div>
                                                        </div>

                                                        <div class="form-group col-md-12 ">
                                                            <label for="is_correct">Is Correct {{ $key+1 }}<span class="text-danger">*</span></label>
                                                            
                                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                <label class="btn btn-secondary active">
                                                                <input type="radio" name="is_correct[{{$key}}]" autocomplete="off" value="1" {{ ($q_options->is_correct == 1) ? ('checked') : ('') }}> Yes
                                                                </label>
                                                                <label class="btn btn-secondary">
                                                                <input type="radio" name="is_correct[{{$key}}]" autocomplete="off" value="0" {{ ($q_options->is_correct == 0) ? ('checked') : ('') }}> No
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <i class="fa fa-trash button button-danger" onclick="deleteOptions('{{$q_options->id }}')" style="padding-left: 950px;"></i>
                                                    </div>
                                                        
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="form-group col-md-12">
                                        <a href="{{ url('admin/assessments/'.$assessment_id.'/questions') }}" class="btn btn-danger">Cancel</a>
                                        <button id="btn-category" type="submit" class="btn btn-primary">Submit</button>
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
    <script type="text/javascript" src="{{ asset('public\Admin\my-script.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();

            var max_fields      = 4; //maximum input boxes allowed
            var wrapper         = $(".my-class"); //Fields wrapper
            var add_button      = $(".add_field_button"); //Add button ID
            
            var x = $('#total_options').val(); //initlal text box count
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    
                    $(wrapper).append(`<hr><div class="form-group col-md-12 ">
                                                    <input type="hidden" name="options_id[]">
                                                    <label for="options">Option<span class="text-danger">*</span></label>
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
                    x++; //text box increment
                }
            });
            
            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })
        });  
    </script>
@endsection