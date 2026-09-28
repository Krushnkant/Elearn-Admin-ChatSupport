<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
//use App\Models\{User,Answer};
use App\Models\{User, TestResult, Answer, UserVideo, Question, Category, Transaction, ChapterVideo, SubscriptionPlan};
use Illuminate\Support\Facades\File;
use DataTables ,Validator, Session, Redirect, Response, DB, Config;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "User List";

    if ($request->ajax())
    {
        $data = User::with('Membership.subscriptionPlan')->orderBy('id', 'desc');
  

                  return Datatables::of($data)
                  ->editColumn('created_at', function($data){
                    return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
                  })
                  ->addColumn('package', 'admin.datatable.action.user.package')
                  ->addColumn('action', 'admin.datatable.action.user.user-action')
                  ->editColumn('status', 'admin.datatable.status.status')
                  ->rawColumns(['package','status', 'action'])
                  ->addIndexColumn()
                  ->make(true);
    }

    return view('admin.user.index', $data);
  }

  public function create(Request $request)
  {
    $data['title'] = "Create User";
    $data['plans'] = SubscriptionPlan::where('status', 1)->orderBy('sort_order', 'asc')->get();
    return view('admin.user.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name'              => 'required',
      'email'             => 'required|email|unique:users',
      'mobile_number'     => 'required|unique:users',
      'password'          => 'required|min:6|max:20',
      'end_date'       => 'required',
    ]);

    $data = [
      'name'            => $request->get('name'),
      'email'           => $request->get('email'),
      'mobile_number'   => $request->get('mobile_number'),
      'password'        => bcrypt($request->get('password')),
      'status'          => $request->get('status'),
      'created_at'      => date('Y-m-d H:i:s'),
    ];

    if($request->hasFile('profile_photo_path')) {
      $files = $request->file('profile_photo_path');
      $destinationPath = 'public/users/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['profile_photo_path'] = $profileImage;
    }
    $insert_id = User::insertGetId($data);

    $future_timestamp = strtotime("+1 month");
    $data1month = date('Y-m-d', $future_timestamp);
    
    $data1month = $request->get('end_date').' 00:00:00';
    if($insert_id) {

      $amount = optional(SubscriptionPlan::find($request->get('planid')))->annual_price ?? 0;


      $datatra = [
            'user_id'           => $insert_id,
            'transaction_id'    => 100,
            'plan'              => $request->get('planid'),
            'amount'            => $amount,
            'start_date'        => date('Y-m-d'),
            'end_date'          => $data1month,
            'status'            => 1,
            'created_at'        => date('Y-m-d H:i:s')
        ];

      Transaction::insert($datatra);

    
      return Redirect::to("admin/users")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/users/create")->withWarning("Oops! Something went wrong");
    }
  }

  public function edit(Request $request, $id)
  {
    $data['title'] = "Edit User";
    $data['user_info'] = User::select('users.*','transactions.plan','transactions.end_date')->leftJoin('transactions', function($join) {
      $join->on('users.id', '=', 'transactions.user_id');
    })->where('users.id', $id)->first();
    $data['plans'] = SubscriptionPlan::where('status', 1)->orderBy('sort_order', 'asc')->get();
    return view('admin.user.edit', $data);
  }

  public function update(Request $request)
  {
    $id = $request->get('id');
    $request->validate([
      'name'              => 'required',
      'email'             => 'required|email|unique:users,email,'.$id,
      'mobile_number'     => 'required|unique:users,mobile_number,'.$id,
      'password'          => 'required|min:6|max:20',
      'end_date'          => 'required'
    ]);

    $data = [
      'name'            => $request->get('name'),
      'email'           => $request->get('email'),
      'mobile_number'   => $request->get('mobile_number'),
      'password'        => bcrypt($request->get('password')),
      'status'          => $request->get('status'),
      'updated_at'      => date('Y-m-d H:i:s'),
    ];
    if($request->hasFile('profile_photo_path')) {
      $image = User::where('id', $id)->select(['profile_photo_path'])->first();
      $d_file = 'public/User/' . $image->originalProfilePhotoPath;
      File::delete($d_file);
      $files = $request->file('profile_photo_path');
      $destinationPath = 'public/users/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['profile_photo_path'] = $profileImage;
    }
    $update = User::where('id', $id)->update($data);
    
    if($update) {

      $amount = optional(SubscriptionPlan::find($request->get('planid')))->annual_price ?? 0;

      $data1month = $request->get('end_date').' 00:00:00';

      $datatra = [
            'plan'              => $request->get('planid'),
            'amount'            => $amount,
            'end_date'            => $data1month,
            'updated_at'        => date('Y-m-d H:i:s')
        ];

      Transaction::where('user_id', $id)->update($datatra);


      return Redirect::to("admin/users")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/users/".$id."/edit")->withWarning("Oops! Something went wrong");
    }
  }


  public function report(Request $request)
  {
    $data['title'] = "Report List";
  
    if ($request->ajax())
    {
    // DataTables server-side pagination: only build rows for the current page
    // instead of computing all 200 (which fires ~15 queries per row and times out).
    $start = (int) $request->get("start", 0);
    $limit = (int) $request->get("length", 10);
    $maxRows = 200;
    $totalRecords = min(TestResult::count(), $maxRows);

    $testids = TestResult::orderBy('id', 'DESC')
        ->limit($maxRows)
        ->pluck('id')
        ->slice($start, $limit > 0 ? $limit : null)
        ->values();

    // Category buckets are the same for every row - fetch once, not per iteration.
    $domain_ids    = Category::where('type', '1')->pluck('id')->toArray();
    $knowledge_ids = Category::where('type', '2')->pluck('id')->toArray();
    $aproch_ids    = Category::where('type', '3')->pluck('id')->toArray();

    $response = [];
    foreach ($testids as $id) {
        $fields = ['id', 'mock_test_id', 'question_id', 'answer_id'];
        $data = Answer::where('mock_test_id', $id)
            ->select($fields)
            ->withCount(['answer as is_correct' => function ($query) {
                $query->where('is_correct', 1);
            }])
            ->with([

                'question:id,category_id,categoryid,sub_category_id',
            ])
            ->get();
     

                       

        $assessment = TestResult::where('id', $id)
            ->select(['id', 'assessment_id', 'created_at as assessment_campletion_date','user_id','reportpdf'])
            ->with('assessment:id,title')->first();

        $user = User::where('id', $assessment->user_id)->select(['name'])->first();     
     
        $total_attempt = TestResult::where('user_id', $assessment->user_id)->count();

        // Reuse the answers already loaded above instead of re-querying the
        // large answers table twice more per row.
        $total = $data->count();
        $q_ids = $data->pluck('question_id')->toArray();

        $domain_ques = Question::whereIn('id', $q_ids)
                                ->whereIn('category_id', $domain_ids)
                ->pluck('id')
                                ->toArray();

    $knowledge_ques = Question::whereIn('id', $q_ids)
                                ->whereIn('categoryid', $knowledge_ids)
                ->pluck('id')
                                ->toArray();

    $aproch_ques = Question::whereIn('id', $q_ids)
                                ->whereIn('sub_category_id', $aproch_ids)
                ->pluck('id')
                                ->toArray();
    
    
  

    $domain_correct = 0;
    $knowledge_correct = 0;
    $aproch_correct = 0;
        $is_correct = 0;
        foreach ($data as $key => $value) {     
            $is_correct = $value->is_correct == Answer::CORRECT ? $is_correct + 1 : $is_correct;
      
      if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$domain_ques))
      {
        $domain_correct = $domain_correct+1;
      }
      if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$knowledge_ques))
      {
        $knowledge_correct = $knowledge_correct+1;
      }
      if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$aproch_ques))
      {
        $aproch_correct = $aproch_correct+1;
      }
     }
     
     
    
    if($total==0)
    {
      $total=1; 
    }
        $total_scored = ($is_correct * 100) / $total;
        if (count($data) > 0) {
            $data->append('passing_percentage');

            $knowledgePercentage = 0;
            $fileName = $assessment->reportpdf;

            $status = "";
            if($total_scored > 70)
            {
              $status = "<span style='color:#44964e;font-weight: bold;'>Pass</span>";
            }
            else
            {
              $status = "<span style='color:#ff0000;font-weight: bold;'>Fail</span>";
            }




            $response[] =         [
                'success'          => true,
                'message'          => "Test result",
                'data'             => $data,
                'total_scored'     => number_format($total_scored, 2) . " %",
                'status'           => $status,
                'assessment'       => $assessment,
                'total_attempt'    =>$total_attempt,
                "passing_percentage" => "70 %",
                'pdf_download'    =>"https://chatsupport.co.in/storage/app/public/pdf/$fileName.pdf",
                'name' => isset($user->name) ? $user->name : ""
              

              
            ];

             //$this->pdf($fileName,$response1);

             // dump($response);
            //$data[] = dump($response);
            // $data1 = array_push($data1,$response);
             //dump($data1);
            // return response()->json($response);
        }
      
       
      }
     //dd($response);
       return Datatables::of($response)
                  ->skipPaging()
                  ->setTotalRecords($totalRecords)
                  ->editColumn('created_at', function($response){
                    return date(Config::get('constants.DATE_FORMAT'), strtotime($response['assessment']->assessment_campletion_date));
                  }) ->escapeColumns('status')->addColumn('action', function($response){

                           // Generate the PDF on demand from the test result id, so there is
                           // no dependency on a pre-saved file or the production domain.
                           $downloadUrl = url('admin/repors/'.$response['assessment']->id.'/download');
                           $btn = '<a href="'.$downloadUrl.'" target="_blank" class="edit btn btn-info btn-sm"><i class="fa fa-download"></i></a> ';
                           $btn = $btn .'<a href="javascript:void(0)" id="905"  class="edit edit-data btn btn-primary btn-sm">Report</a>';

                            return $btn;
                    })
                    ->rawColumns(['action'])->addIndexColumn()->make(true);
    }
     
    return view('admin.user.report', $data);
  }

  /**
   * Generate and stream the assessment result PDF on demand.
   * Avoids relying on a pre-saved file / hardcoded production URL.
   */
  public function downloadReport(Request $request, $id)
  {
    $testResult = TestResult::find($id);
    if (!$testResult) {
      abort(404, 'Test result not found.');
    }

    $owner = User::find($testResult->user_id);
    if (!$owner) {
      abort(404, 'Result owner not found.');
    }

    // Reuse the existing report builder; it expects a request whose user() is the result owner.
    $sub = Request::create('/admin/repors/'.$id.'/test-results', 'GET');
    $sub->setUserResolver(function () use ($owner) {
      return $owner;
    });

    $payload = json_decode($this->testResultgraf($sub, $id)->getContent(), true);
    if (empty($payload['success'])) {
      abort(404, 'No report data available for this result.');
    }

    return \PDF::setPaper('a4')
      ->loadView('admin.pdf.test-results', ['data' => $payload, 'user' => $owner])
      ->download('assessment-report-'.$id.'.pdf');
  }



   public function testResultgraf(Request $request, $id)
    {
       
        $categories = Category::where('status', 1)->get(['id', 'name', 'type']);
    
        $fields = ['id', 'mock_test_id', 'question_id', 'answer_id'];
        $data = Answer::where('mock_test_id', $id)
            ->select($fields)
            ->withCount(['answer as is_correct' => function ($query) {
                $query->where('is_correct', 1);
            }])
            ->with([

                'question:id,category_id,categoryid,sub_category_id',
            ])
            ->get();

        $allDomainAnswers = $data->filter(function ($question) {
      
            return $question->question->category->type == Category::DOMAIN;
        });
       
        $allKnowledgeAnswers = $data->filter(function ($question) {
            return $question->question->category->type == Category::KNOWLEDGE;
        });

        $allApprochAnswers = $data->filter(function ($question) {
      
            return $question->question->category->type == Category::APPROACH;
        });

        $correctAnswers = $data->where('is_correct', 'correct');

        $domainAnswers = $correctAnswers->filter(function ($question) {
      
            return $question->question->category->type == Category::DOMAIN;
        });

        $knowledgeAnswers = $correctAnswers->filter(function ($question) {
            return $question->question->category->type == Category::KNOWLEDGE;
        });

        $approchAnswers = $correctAnswers->filter(function ($question) {
            return $question->question->category->type == Category::APPROACH;
        });

        $categories = $categories->map(function ($item, $key) use ($correctAnswers, $data) {

            $item->totalQuestion = $data->where('question.category.id', $item->id)->count();
            $item->correctQuestion = $correctAnswers->where('question.category.id', $item->id)->count();
            $item->scored = $this->getPercentage($item->correctQuestion, $item->totalQuestion);
            return $item;
        });

        $allDomainCategory = $categories->where('type', Category::DOMAIN);
        $allKnowledgeCategory = $categories->where('type', Category::KNOWLEDGE);
        $allApprochCategory = $categories->where('type', Category::APPROACH);


        $domain = Answer::where(['mock_test_id' => $id])
            ->select($fields)
            ->with(['question' => function ($query) {
                $query->where('category_id', 2)
                    ->select(['id', 'category_id', \DB::raw('id as correct')]);
            }])
            ->get();

        $assessment = TestResult::where('id', $id)
            ->select(['id', 'assessment_id', 'created_at as assessment_campletion_date'])
            ->with('assessment:id,title')->first();

        $total_attempt = TestResult::where('user_id', $request->user()->id)->count();

        $total = Answer::where('mock_test_id', $id)->select(['question_id'])->count();

        
    $q_ids = Answer::where('mock_test_id', $id)->pluck('question_id')->toArray();        
    $domain_ids = Category::where('type', '1')->pluck('id')->toArray();
        $domain_ques = Question::whereIn('id', $q_ids)
                                ->whereIn('category_id', $domain_ids)
                ->pluck('id')
                                ->toArray();
    
    $knowledge_ids = Category::where('type', '2')->pluck('id')->toArray();
    $knowledge_ques = Question::whereIn('id', $q_ids)
                                ->whereIn('categoryid', $knowledge_ids)
                ->pluck('id')
                                ->toArray();
                
    $aproch_ids = Category::where('type', '3')->pluck('id')->toArray();
    $aproch_ques = Question::whereIn('id', $q_ids)
                                ->whereIn('sub_category_id', $aproch_ids)
                ->pluck('id')
                                ->toArray();
    
    
    $domain_correct = 0;
    $knowledge_correct = 0;
    $aproch_correct = 0;
        $is_correct = 0;
        foreach ($data as $key => $value) {     
            $is_correct = $value->is_correct == Answer::CORRECT ? $is_correct + 1 : $is_correct;
      
      if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$domain_ques))
      {
        $domain_correct = $domain_correct+1;
      }
      if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$knowledge_ques))
      {
        $knowledge_correct = $knowledge_correct+1;
      }
      if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$aproch_ques))
      {
        $aproch_correct = $aproch_correct+1;
      }
     }
     
     
     // Domain sub-category scores
     $final_domain_array = array();
     foreach($allDomainCategory->values()->toArray() as $v)
     {
       $domaain_ques_s = Question::whereIn('id', $q_ids)
                                ->where('category_id', $v['id'])
                ->pluck('id')
                                ->toArray();
      
      $domain_correct_s = 0;
      foreach ($data as $key => $value) { 
        if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$domaain_ques_s))
        {
          $domain_correct_s = $domain_correct_s+1;
        }
      }
      
       $ab['id'] = $v['id'];
       $ab['name'] = $v['name'];
       $ab['type'] = $v['type'];
       $ab['totalQuestion'] = count($domaain_ques_s);
       $ab['correctQuestion'] = $domain_correct_s;
       $ab['scored'] = $this->getPercentage(
                            $domain_correct_s,
                            count($domaain_ques_s)
                        );
       $final_domain_array[] = $ab;
     }
     // Knowledge sub-category scores
     $final_Knowledge_array = array();
     foreach($allKnowledgeCategory->values()->toArray() as $v)
     {
       $knowledge_ques_s = Question::whereIn('id', $q_ids)
                                ->where('categoryid', $v['id'])
                ->pluck('id')
                                ->toArray();
      
      $knowledge_correct_s = 0;
      foreach ($data as $key => $value) { 
        if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$knowledge_ques_s))
        {
          $knowledge_correct_s = $knowledge_correct_s+1;
        }
      }
      
       $ab['id'] = $v['id'];
       $ab['name'] = $v['name'];
       $ab['type'] = $v['type'];
       $ab['totalQuestion'] = count($knowledge_ques_s);
       $ab['correctQuestion'] = $knowledge_correct_s;
       $ab['scored'] = $this->getPercentage(
                            $knowledge_correct_s,
                            count($knowledge_ques_s)
                        );
       $final_Knowledge_array[] = $ab;
     }
     
     // Approach sub-category scores
     $final_Approch_array = array();
     foreach($allApprochCategory->values()->toArray() as $v)
     {
       $aproch_ques_s = Question::whereIn('id', $q_ids)
                                ->where('sub_category_id', $v['id'])
                ->pluck('id')
                                ->toArray();
      
      $aproch_correct_s = 0;
      foreach ($data as $key => $value) { 
        if($value->is_correct == Answer::CORRECT && in_array($value->question_id,$aproch_ques_s))
        {
          $aproch_correct_s = $aproch_correct_s+1;
        }
      }
      
       $ab['id'] = $v['id'];
       $ab['name'] = $v['name'];
       $ab['type'] = $v['type'];
       $ab['totalQuestion'] = count($aproch_ques_s);
       $ab['correctQuestion'] = $aproch_correct_s;
       $ab['scored'] = $this->getPercentage(
                            $aproch_correct_s,
                            count($aproch_ques_s)
                        );
       $final_Approch_array[] = $ab;
     }
     
     
    //echo "<pre>"; print_r($final_Approch_array);die;
    if($total==0)
    {
      $total=1; 
    }
        $total_scored = ($is_correct * 100) / $total;
        if (count($data) > 0) {
            $data->append('passing_percentage');

            $knowledgePercentage = 0;
            $fileName = Carbon::now()->format('YmdHs');
            $response =         [
                'success'          => true,
                'message'          => "Test result",
                'data'             => $data,
                'total_scored'     => $total_scored . " %",
                'status'           => $total_scored > 60 ? "Pass" : "Fail",
                'assessment'       => $assessment,
                'total_attempt'    =>$total_attempt,
                "passing_percentage" => "70 %",
                'pdf_download'    =>"https://chatsupport.co.in/storage/app/public/pdf/$fileName.pdf",
                "categoryWiseReport" => [
                    [
                        "title" => "Domain",
                        "totalQuestion" => count($domain_ques),
                        "correctQuestion" => $domain_correct,
                        "scored" => $this->getPercentage(
                            $domain_correct,
                            count($domain_ques)
                        ),
                        "category" => $final_domain_array
                    ],
                    [
                        "title" => "Performance Domain", 
                        "totalQuestion" => count($knowledge_ques),
                        "correctQuestion" => $knowledge_correct,
                        "scored" => $this->getPercentage(
                            $knowledge_correct,
                            count($knowledge_ques)
                        ),
                        "category" => $final_Knowledge_array
                    ],
                    [
                        "title" => "Approch",
                        "totalQuestion" => count($aproch_ques),
                        "correctQuestion" => $aproch_correct,
                        "scored" => $this->getPercentage(
                            $aproch_correct,
                            count($aproch_ques)
                        ),
                        "category" => $final_Approch_array
                    ]
                ]
            ];

    
        $data1 = [
         'reportpdf' => $fileName,
       ];
       $update = TestResult::where('id', $id)->update($data1);
       return response()->json($response);
        }

        return response()->json([
            'success'          => false,
            'message'          => "Data not found",
        ]);
    }



  private function getPercentage($value, $totalValue)
  {
        if ($totalValue > 0) {
            return ($value * 100) / $totalValue;
        }
        return 0;
  }

    private function pdf($fileName,$data)
    {
         \PDF::setPaper('a4')
        // ->setOrientation('landscape')
        // ->setOption('enable-javascript', true)
        // ->setOption('javascript-delay',5000)
        // ->setOption('images', true)
        // ->setOption('enable-smart-shrinking', true)
        // ->setOption('no-stop-slow-scripts', true)
        ->loadView('admin.pdf.test-results', ['data' => $data,'user'=>$data])
        ->save(storage_path('app/public/pdf')."/$fileName.pdf");
    }
}

