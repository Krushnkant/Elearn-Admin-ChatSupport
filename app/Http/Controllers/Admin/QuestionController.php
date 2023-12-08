<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File;
use App\Models\{Question, Category, Course, QuestionOption, CategoryQuestion, Assessment};
use DataTables;
use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Database\QueryException;

class QuestionController extends Controller
{
  public function index(Request $request, $assessment_id)
  {
    $data['title'] = "Question List";
    $data['assessment_id'] = $assessment_id;
    if ($request->ajax())
    {
      $data = Question::where('assessment_id', decode($assessment_id))->with(['course','category:id,name,type', 'assessment'])->orderBy('id', 'desc');
                return Datatables::of($data)
                ->editColumn('created_at', function($data){
                  return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
                })
                ->addColumn('action', 'admin.datatable.action.question.question-action')
                ->editColumn('status', 'admin.datatable.status.category-status')
                ->rawColumns(['status', 'action'])
                ->addIndexColumn()
                ->make(true);
    }

    return view('admin.question.index', $data);
  }

  public function create(Request $request, $assessment_id)
  {
    $data['title'] = "Create Question";
    $data['category_info'] = Category::get();
    $data['assessment_id'] = $assessment_id;
    $data['course_info'] = Course::get();
    return view('admin.question.add', $data);
  }

  public function store(Request $request)
  {

    $course = Assessment::where('id', decode($request->get('assessment_id')))->select(['course_id'])->first();
    $request->validate([
      'set_type'       => 'required',
      'domain_id'       => 'required',
      'knowledge_id'      => 'required',
      'approach_id'       => 'required',
      'title'             => 'required',
      'explanation'       => 'required',
      'dificulty_level'   => 'required',
      'marks'             => 'required',
      'question_type'     => 'required',
      'options.*'         => 'required',
    ],
    [
      'category_id.required'    => " The category field is required.",
      'knowledge_id.required'   => " The knowledge field is required.",
      'approach_id.required'    => " The approach field is required.",
      'options.*.required'      => " The options field is required.",
    ]);

    \DB::beginTransaction();
    try {


      if(!empty($request->get('domain_id'))) {

        $domain = $request->get('domain_id');
        
        $domain_info = [];
        $is_domain_exists = Category::where('type', 1)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_domain_exists)) {
            foreach($is_domain_exists as $kn) {
                $domain_info[] = [
                    'set_type'             => $request->get('set_type'),
                   'category_id'   =>  1,
                   'sub_category_id'   =>  $kn->id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                    
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($domain_info);
                
            }
         
            Question::insert($domain_info);
            // $insert_id = Question::insertGetId($domain_info);
             $insert_id1 = DB::getPDO()->lastInsertId();
          // dd ($domain_info); 
            //$insert_id1 = Question::insertGetId($domain_info);
            
            if(!empty($request->get('options'))) {
              $options = $request->get('options');
              $image = $request->file('image');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $insert_id1,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
      
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                  $destinationPath = 'public/options/'; // upload path
                  $rand = md5(time() . mt_rand(100000000, 999999999));
                  $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                  $files[$key]->move($destinationPath, $profileImage);
                  $que_options['image'] = $profileImage;
                }
                QuestionOption::insert($que_options);
              }
            }
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(!empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
     
        $knowledge_info = [];
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
            foreach($is_knowledge_exists as $kn) {
                $knowledge_info[] = [
                  'set_type'             => $request->get('set_type'),
                  'category_id'   =>  2,
                  'sub_category_id'   => $kn->id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                   
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
                //Question::insert($knowledge_info);
            }
            Question::insert($knowledge_info);
            // $insert_id = Question::insertGetId($domain_info);
             $insert_id2 = DB::getPDO()->lastInsertId();
            //$insert_id1 =  Question::insertGetId($knowledge_info);
            if(!empty($request->get('options'))) {
              $options = $request->get('options');
              $image = $request->file('image');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $insert_id2,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
      
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                  $destinationPath = 'public/options/'; // upload path
                  $rand = md5(time() . mt_rand(100000000, 999999999));
                  $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                  $files[$key]->move($destinationPath, $profileImage);
                  $que_options['image'] = $profileImage;
                }
                QuestionOption::insert($que_options);
              }
            }
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(!empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
      
        $approach_info = [];
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_approach_exists)) {
            foreach($is_approach_exists as $kn) {
                $approach_info[] = [
                  'set_type'             => $request->get('set_type'),
                  'category_id'   =>  3,
                  'sub_category_id'   => $kn->id,
                  'course_id'         => $course->course_id,
                  'assessment_id'     => decode($request->get('assessment_id')),
                  'marks'             => $request->get('marks'),
                  'title'             => $request->get('title'),
                  'explanation'       => $request->get('explanation'),
                  'dificulty_level'   => $request->get('dificulty_level'),
                  
                  'question_type'     => $request->get('question_type'),
                  'status'            => !empty($request->get('status')) ? 1 : 0,
                  'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($approach_info);
            }
            Question::insert($approach_info);
            // $insert_id = Question::insertGetId($domain_info);
             $insert_id3 = DB::getPDO()->lastInsertId();
            // $insert_id1 =  Question::insertGetId($approach_info);
            if(!empty($request->get('options'))) {
              $options = $request->get('options');
              $image = $request->file('image');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $insert_id3,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
      
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                  $destinationPath = 'public/options/'; // upload path
                  $rand = md5(time() . mt_rand(100000000, 999999999));
                  $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                  $files[$key]->move($destinationPath, $profileImage);
                  $que_options['image'] = $profileImage;
                }
                QuestionOption::insert($que_options);
              }
            }
          //CategoryQuestion::insert($approach_info);
        }
      }

      if(!empty($request->get('domain_id'))) {
        $domain = $request->get('domain_id');
        $knowledge_info = [];
        $is_knowledge_exists = Category::where('type', 1)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
            foreach($is_knowledge_exists as $kn) {
                $knowledge_info[] = [
                  'question_id'   => $insert_id1,
                  'set_type'   =>  $request->get('set_type'),
                    'category_id'   =>1,
                    'sub_category_id'   => $kn->id,
                    
                ];
            }
          CategoryQuestion::insert($knowledge_info);
        }
      }
      if(!empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
        $knowledge_info = [];
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
            foreach($is_knowledge_exists as $kn) {
                $knowledge_info[] = [
                  'question_id'   => $insert_id1,
                  'set_type'   =>  $request->get('set_type'),
                  'category_id'   => 2,
                    'sub_category_id'   => $kn->id,
                ];
            }
          CategoryQuestion::insert($knowledge_info);
        }
      }

      if(!empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
        $approach_info = [];
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_approach_exists)) {
            foreach($is_approach_exists as $kn) {
                $approach_info[] = [
                  'question_id'   => $insert_id1,
                  'set_type'   =>  $request->get('set_type'),
                    'category_id'   =>3,
                    'sub_category_id'   => $kn->id,
                ];
            }
          CategoryQuestion::insert($approach_info);
        }
      }
      \DB::commit();
      if($insert_id1) {
      return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions/create")->withWarning("Oops! Something went wrong");
    }
    } catch (Exception $e) {
      \DB::rollback();
      throw $e;
    }
    
  }


  public function getImportCSV(Request $request, $assessment_id)
  {
    $data['title'] = "Import CSV";
    $data['assessment_id'] = decode($assessment_id);
    return view('admin.question.import-csv', $data);
  }


  public function postImportCSV(Request $request)
  {
    $request->validate([
      'question_csv' => 'required'
    ]);

    //$excel = $request->file('question_csv');
    $path1 = $request->file('question_csv')->store('temp'); 
    $filepath=storage_path('app').'/'.$path1;
    //dd($path);  
    //$file = $excel->getRealPath();
    
	$file = fopen($filepath,"r");
	$importData_arr = array();
	$i = 0;
		while (($filedata = fgetcsv($file, 10000000, ",")) !== FALSE)
		{
			$num = count($filedata );		 
			 // Skip first row (Remove below comment if you want to skip the first row)
			 /*if($i == 0){
				$i++;
				continue; 
			 }*/
			for ($c=0; $c < $num; $c++) {
				$importData_arr[$i][] = $filedata [$c];
			}
			$i++;
		}
		fclose($file);
	//echo "<pre>"; print_r($importData_arr);die;
	//dd($importData_arr);
	$total_record = count($importData_arr);
	for($i=1;$i<$total_record;$i++)
	{
          
         
	if(
		isset($importData_arr[$i]['0']) && $importData_arr[$i]['0']!=""
		&& isset($importData_arr[$i]['22'])&& $importData_arr[$i]['22']!=""
		&& isset($importData_arr[$i]['11'])&& $importData_arr[$i]['11']!=""
		&& isset($importData_arr[$i]['2'])&& $importData_arr[$i]['2']!=""
		&& isset($importData_arr[$i]['19'])&& $importData_arr[$i]['19']!=""
		&& isset($importData_arr[$i]['17'])&& $importData_arr[$i]['17']!=""
		)
	{

        	// if(
	// 	isset($importData_arr[$i]['18']) &&  $importData_arr[$i]['18']!=""
	// 	&& isset($importData_arr[$i]['0']) && $importData_arr[$i]['0']!=""
	// 	&& isset($importData_arr[$i]['22'])&& $importData_arr[$i]['22']!=""
	// 	&& isset($importData_arr[$i]['11'])&& $importData_arr[$i]['11']!=""
	// 	&& isset($importData_arr[$i]['2'])&& $importData_arr[$i]['2']!=""
	// 	&& isset($importData_arr[$i]['19'])&& $importData_arr[$i]['19']!=""
	// 	&& isset($importData_arr[$i]['17'])&& $importData_arr[$i]['17']!=""
	// 	&& isset($importData_arr[$i]['13'])&& $importData_arr[$i]['13']!=""
	// 	&& isset($importData_arr[$i]['14'])&& $importData_arr[$i]['14']!=""
	// 	)
	// {
               	
		//$category_data = DB::table('categories')->where('name', $importData_arr[$i]['13'])->first();		
		//$category_id = $category_data->id;
		
		//$categorydata = DB::table('categories')->where('name', $importData_arr[$i]['14'])->first();		
		//$categoryid = $categorydata->id;
		//$category_id = $category_data->type;
		
		//$sub_category_data = DB::table('categories')->where('name', $importData_arr[$i]['18'])->first();		
		//$sub_category_id = $sub_category_data->id;

                $category_data = DB::table('categories')->where('name', $importData_arr[$i]['13'])->first();	
                if(isset($category_data->id)){
                     $category_id = $category_data->id;
                }else{
                     $category_id = 0;
                }	
		//$category_id = ($category_data->id)?$category_data->id:0;
		
		$categorydata = DB::table('categories')->where('name', $importData_arr[$i]['14'])->first();
                if(isset($categorydata->id)){
                     $categoryid = $categorydata->id;
                }else{
                     $categoryid = 0;
                }		
		//$categoryid = ($categorydata->id)?$categorydata->id:0;
		//$category_id = $category_data->type;
		
		$sub_category_data = DB::table('categories')->where('name', $importData_arr[$i]['18'])->first();
                if(isset($sub_category_data->id)){
                     $sub_category_id = $sub_category_data->id;
                }else{
                     $sub_category_id = 0;
                }		
		//$sub_category_id = ($sub_category_data->id)?$sub_category_data->id:0;
                
                if(isset($importData_arr[$i]['30']) && $importData_arr[$i]['30']!=""){
                   $enabler = $importData_arr[$i]['30'];
                }else{
                   $enabler = "";
                }
		
		
		$values = array(
			'set_type' 			=> $importData_arr[$i]['0'],
			'category_id' 		=> $category_id,
			'categoryid' 		=> $categoryid,
			'sub_category_id' 	=> $sub_category_id,
			'course_id' 		=> '1',
			'assessment_id' 	=> $importData_arr[$i]['22'],
			'marks' 			=> $importData_arr[$i]['11'],
			'title' 			=> $importData_arr[$i]['2'],
			'explanation' 		=> $importData_arr[$i]['19'],
                        'enabler' 		=> $enabler,
			'dificulty_level' 	=> $importData_arr[$i]['17'],
			'status'            => '1',
			'created_at'        => date('Y-m-d H:i:s') 
			);
		DB::table('questions')->insert($values);
		$question_id = DB::getPdo()->lastInsertId();
		
		//1st Option
		if($importData_arr[$i]['4']!="")
		{
			if($importData_arr[$i]['10']=='1' OR $importData_arr[$i]['10']=='1,2' OR $importData_arr[$i]['10']=='1,3' OR $importData_arr[$i]['10']=='1,4' OR $importData_arr[$i]['10']=='1,5' OR $importData_arr[$i]['10']=='1,6')
			{
				$is_correct = 1;
			}else{
				$is_correct = 0;
			}
			$que_options1 = array(
                  'question_id'   => $question_id,
                  'options'       => $importData_arr[$i]['4'],
                  'is_correct'    => $is_correct,
                  'created_at'    => date('Y-m-d H:i:s')
                );
			DB::table('question_options')->insert($que_options1);	
		}
		//2nd Option
		if($importData_arr[$i]['5']!="")
		{
			if($importData_arr[$i]['10']=='2' OR $importData_arr[$i]['10']=='1,2' OR $importData_arr[$i]['10']=='2,3' OR $importData_arr[$i]['10']=='2,4' OR $importData_arr[$i]['10']=='2,5' OR $importData_arr[$i]['10']=='2,6')
			{
				$is_correct = 1;
			}else{
				$is_correct = 0;
			}
			$que_options2 = array(
                  'question_id'   => $question_id,
                  'options'       => $importData_arr[$i]['5'],
                  'is_correct'    => $is_correct,
                  'created_at'    => date('Y-m-d H:i:s')
                );
			DB::table('question_options')->insert($que_options2);	
		}
		
		//3rd Option
		if($importData_arr[$i]['6']!="")
		{
			if($importData_arr[$i]['10']=='3' OR $importData_arr[$i]['10']=='1,3' OR $importData_arr[$i]['10']=='2,3' OR $importData_arr[$i]['10']=='3,4' OR $importData_arr[$i]['10']=='3,5' OR $importData_arr[$i]['10']=='3,6')
			{
				$is_correct = 1;
			}else{
				$is_correct = 0;
			}
			$que_options3 = array(
                  'question_id'   => $question_id,
                  'options'       => $importData_arr[$i]['6'],
                  'is_correct'    => $is_correct,
                  'created_at'    => date('Y-m-d H:i:s')
                );
			DB::table('question_options')->insert($que_options3);	
		}
		
		//4rth Option
		if($importData_arr[$i]['7']!="")
		{
			if($importData_arr[$i]['10']=='4' OR $importData_arr[$i]['10']=='1,4' OR $importData_arr[$i]['10']=='2,4' OR $importData_arr[$i]['10']=='3,4' OR $importData_arr[$i]['10']=='4,5' OR $importData_arr[$i]['10']=='4,6')
			{
				$is_correct = 1;
			}else{
				$is_correct = 0;
			}
			$que_options4 = array(
                  'question_id'   => $question_id,
                  'options'       => $importData_arr[$i]['7'],
                  'is_correct'    => $is_correct,
                  'created_at'    => date('Y-m-d H:i:s')
                );
			DB::table('question_options')->insert($que_options4);	
		}
		
		//5th Option
		if($importData_arr[$i]['8']!="")
		{
			if($importData_arr[$i]['10']=='5' OR $importData_arr[$i]['10']=='1,5' OR $importData_arr[$i]['10']=='2,5' OR $importData_arr[$i]['10']=='3,5' OR $importData_arr[$i]['10']=='4,5' OR $importData_arr[$i]['10']=='5,6')
			{
				$is_correct = 1;
			}else{
				$is_correct = 0;
			}
			$que_options5 = array(
                  'question_id'   => $question_id,
                  'options'       => $importData_arr[$i]['8'],
                  'is_correct'    => $is_correct,
                  'created_at'    => date('Y-m-d H:i:s')
                );
			DB::table('question_options')->insert($que_options5);	
		}
		
		//6th Option
		if($importData_arr[$i]['9']!="")
		{
			if($importData_arr[$i]['10']=='6' OR $importData_arr[$i]['10']=='1,6' OR $importData_arr[$i]['10']=='2,6' OR $importData_arr[$i]['10']=='3,6' OR $importData_arr[$i]['10']=='4,6' OR $importData_arr[$i]['10']=='5,6')
			{
				$is_correct = 1;
			}else{
				$is_correct = 0;
			}
			$que_options6 = array(
                  'question_id'   => $question_id,
                  'options'       => $importData_arr[$i]['9'],
                  'is_correct'    => $is_correct,
                  'created_at'    => date('Y-m-d H:i:s')
                );
			DB::table('question_options')->insert($que_options6);	
		}
		
		
		//echo "<pre>"; print_r($category_data);die;
	}
	}
	
 	return Redirect::to("admin/assessments/".encode($request->get('assessment_id'))."/questions")->withSuccess("Great! Info has been added");
	
   /* $param = Excel::import(new QuestionsImport, $file);
    if($param) {
      return Redirect::to("admin/assessments/".encode($request->get('assessment_id'))."/questions")->withSuccess("Great! Info has been added");
    }
    return Redirect::to("admin/assessments/".encode($request->get('assessment_id'))."/import-csv")->withSuccess("Oops! Something went wrong");
	*/
 }

  public function hardDelete(Request $request){
    $id = $request->get('id');
    $where = array('id' => $id);
    $question_info = Question::where('id', $id)->first();
    $question_info->questionOptions()->delete();
    $question_info->categoryQuestion()->delete();
    $result = $question_info->delete();
    if ($result) {
      die('1');
    } else {
      die('0');
    }
  }

  public function edit(Request $request, $assessment_id, $id)
  {
    $data['title'] = "Edit Question";
    $data['category_info'] = Category::get();
    $data['course_info'] = Course::get();
    $data['assessment_id'] = $assessment_id;
    $data['id'] = $id;
    $data['question_info'] = Question::where('id', decode($id))->with(['questionOptions', 'categoryQuestion'])->first();
    return view('admin.question.edit', $data);
  }

  public function update(Request $request)
  {
    //dd($request->all());
    $id = $request->get('id');
    $course = Assessment::where('id', decode($request->get('assessment_id')))->select(['course_id'])->first();
    //dd($course);
    $request->validate([
   //   'domain_id'       => 'required',
    //  'knowledge_id'       => 'required',
   //   'approach_id'       => 'required',
      'title'             => 'required',
      'explanation'       => 'required',
      'dificulty_level'   => 'required',
      'marks'             => 'required',
      'question_type'     => 'required',
    ]);
    \DB::beginTransaction();
    try {
    
       if(!isset($request->domain_id) && !isset($request->knowledge_id) && !isset($request->approach_id) && !isset($request->knowledge_id)){

      
      
     
                $domain_info = [
                   'category_id'   =>  1,
                   //'sub_category_id'   =>  $kn->id,
                    'sub_category_id'   =>  0,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                    
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($domain_info);
                
         
            //Question::insert($domain_info);
            $update =  Question::where('id', $id)->update($domain_info);
            
         
             if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      

       }


      if(isset($request->domain_id) && !empty($request->get('domain_id'))) {
         
        $domain = $request->get('domain_id');
        
        $domain_info = "";
        $is_domain_exists = Category::where('type', 1)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_domain_exists)) {
          $cat_id = [];
            foreach($is_domain_exists as $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              //$cat_id[] = $kn->id;
              $cat_id[] = $kn_id;
                $domain_info = [
                   'category_id'   =>  1,
                   //'sub_category_id'   =>  $kn->id,
                    'sub_category_id'   =>  $kn_id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                    
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($domain_info);
                
            }
         
            //Question::insert($domain_info);
            $update =  Question::where('id', $id)->update($domain_info);
            
             
          
            
             if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(isset($request->knowledge_id) && !empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
     
        $knowledge_info = "";
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
          $cat_id = [];
            foreach($is_knowledge_exists as $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              $cat_id[] = $kn_id;
                $knowledge_info = [
                  'category_id'   =>  2,
                  'sub_category_id'   => $kn_id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                   
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
                //Question::insert($knowledge_info);
            }
            $update =  Question::where('id', $id)->update($knowledge_info);
            
            
            
             if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(isset($request->approach_id) && !empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
      
        $approach_info = "";
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_approach_exists)) {
          $cat_id = [];
            foreach($is_approach_exists as $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              $cat_id[] = $kn_id;
                $approach_info = [
                  'category_id'   =>  3,
                  'sub_category_id'   => $kn_id,
                  'course_id'         => $course->course_id,
                  'assessment_id'     => decode($request->get('assessment_id')),
                  'marks'             => $request->get('marks'),
                  'title'             => $request->get('title'),
                  'explanation'       => $request->get('explanation'),
                  'dificulty_level'   => $request->get('dificulty_level'),
                  
                  'question_type'     => $request->get('question_type'),
                  'status'            => !empty($request->get('status')) ? 1 : 0,
                  'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($approach_info);
            }
            $update =    Question::where('id', $id)->update($approach_info);
            
            if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      
          //CategoryQuestion::insert($approach_info);
        }
      }

      

      if(isset($request->domain_id) && !empty($request->get('domain_id'))) {
        $domain = $request->get('domain_id');
        $domain_info = "";
        $is_domain_exists = Category::where('type', 2)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
                                        //dd(count($is_knowledge_exists));
        if(!empty($is_domain_exists)) {
          $cat_id = [];
          foreach($is_domain_exists as $key => $kn) {
            if(isset($kn->id)){
             $kn_id =  $kn->id;
            }else{
              $kn_id = 0;
            }
            $cat_id[] = $kn_id;
            $domain_info = [
              'category_id'   => $kn_id,
              'question_id'   => $id,
            ];
            CategoryQuestion::updateOrCreate(['question_id' => $id, 'category_id' => $kn_id], $domain_info);
          }
        }
      }

      if(isset($request->knowledge_id) && !empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
        $knowledge_info = "";
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
                                        //dd(count($is_knowledge_exists));
        if(!empty($is_knowledge_exists)) {
          if(isset($kn->id)){
             $kn_id =  $kn->id;
            }else{
              $kn_id = 0;
            }
          $cat_id = [];$cat_id[] = $kn_id;
          foreach($is_knowledge_exists as $key => $kn) {
            if(isset($kn->id)){
             $kn_id =  $kn->id;
            }else{
              $kn_id = 0;
            }
            $cat_id[] = $kn_id;
            $knowledge_info = [
              'category_id'   => $kn_id,
              'question_id'   => $id,
            ];
            CategoryQuestion::updateOrCreate(['question_id' => $id, 'category_id' => $kn_id], $knowledge_info);
          }
        }
      }

      if(isset($request->approach_id) && !empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
        $approach_info = "";
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
                                        //dd(count($is_knowledge_exists));
        if(!empty($is_approach_exists)) {
            $cat_id = [];
            foreach($is_approach_exists as $key => $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              $cat_id[] = $kn_id;
              $approach_info = [
                'category_id'   => $kn_id,
                'question_id'   => $id,
              ];
              CategoryQuestion::updateOrCreate(['question_id' => $id, 'category_id' => $kn_id], $approach_info);
            }
        }
      }

      \DB::commit();
    //  if($update) {
        return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions")->withSuccess("Great! Info has been updated");
    //  } else {
    //    return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions/".$request->get('id')."edit")->withWarning("Oops! Something went wrong");
   //   }
    } catch (Exception $e) {
      \DB::rollback();
      throw $e;
    }
  }

  public function createSlug($name, $id = Null)
    {
        $slug = \Str::slug($name);
        $is_exists = $this->getRelatedSlugs($slug, $id);

        if($is_exists == 0) {
          return $slug;
        }

        for ($i = 1; $i <= 10; $i++) {
          $newSlug = $slug.'-'.$i;
          $unique = $this->getRelatedSlugs($newSlug, $id);
          if($unique == 0) {
            return $newSlug;
          }
        }
        throw new \Exception('Can not create a unique slug');
      }

    protected function getRelatedSlugs($slug, $id = Null)
        {
            $query = Question::query();
            if($id){
            $query->where('id','!=',$id);     
        }
        return $query->select('slug')
                    ->where('slug', $slug)
                    ->count();
    }

}

