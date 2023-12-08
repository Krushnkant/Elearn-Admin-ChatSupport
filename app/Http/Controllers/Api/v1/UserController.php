<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\{User, TestResult, Answer, UserVideo, Question, Category, Transaction, ChapterVideo};
use Carbon\Carbon;
use Response, DB, Mail;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user_info = new UserResource($request->user());
        return response()->json([
            'success'          => true,
            'message'          => "Data successfully found",
            'data'             => $user_info,
        ]);
    }

    public function testResult(Request $request, $id)
    {

        $categories = Category::where('status', 1)->get(['id', 'name', 'type']);
        
        $fields = ['id', 'mock_test_id', 'question_id', 'answer_id','is_correctans as is_correct'];
        DB::enableQueryLog(); // Enable query log
        $data = Answer::where('mock_test_id', $id)
            ->select($fields)
            // ->withCount(['answer as is_correct' => function ($query) {
            //     $query->where('is_correct', 1);
            // }])
            ->with([
                'question:id,category_id,categoryid,sub_category_id,title,explanation,enabler','questionOptionNew'
            ])
            ->get();
         //dd($data->toArray());
        //  dd($data->toArray());
         //   $queries =DB::getQueryLog();
        //     dd($queries);
        
        
        $allDomainAnswers = $data->filter(function ($question) {
         if(isset($question->question->category)){
                 return $question->question->category->type == Category::DOMAIN;
             }  
            
        });
        
       
        $allKnowledgeAnswers = $data->filter(function ($question) {
            if(isset($question->question->category)){
                 return $question->question->category->type == Category::KNOWLEDGE;
             }
            
        });

        $allApprochAnswers = $data->filter(function ($question) { 
            if(isset($question->question->category)){
                 return $question->question->category->type == Category::APPROACH;
             }          
            
        });

        $correctAnswers = $data->where('is_correct', 'correct');

        $domainAnswers = $correctAnswers->filter(function ($question) {
            if(isset($question->question->category)){
                 return $question->question->category->type == Category::DOMAIN;
             } 
            
        });

        $knowledgeAnswers = $correctAnswers->filter(function ($question) {
            
            if(isset($question->question->category)){
                return $question->question->category->type == Category::KNOWLEDGE;
             }
        });

        $approchAnswers = $correctAnswers->filter(function ($question) {
            if(isset($question->question->category)){
                return $question->question->category->type == Category::APPROACH;
             }
            
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
            //echo "<pre>"; print_r($domain);die;
        /*->whereHas('question', function($query) {
                            $query->where('category_id', 2)
                                    ->select(['id', 'category_id',])->count();
                        })*/
        /*->withCount(['answer as is_correct' => function($query) {
                            $query->where('is_correct', 1)->count();
                        }])

                        ->get();*/

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
        
        
       /* $group['domain_correct'] = Answer::where('mock_test_id', $id)
                                            ->whereIn('question_id', $q_ids)
                                            ->select($fields)
                                            ->withCount(['answer as is_correct' => function($query) {
                                                $query->where('is_correct', 1);
                                            }]);
                                            //->count();*/

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
       
       
       /*===============================Domain===================  */
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
       /*===============================Knoledge===================  */
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
       
       /*===============================Approch===================  */
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

        $this->pdf($fileName,$response);
    
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
        ->loadView('admin.pdf.test-results', ['data' => $data,'user'=>Auth::user()])
        ->save(storage_path('app/public/pdf')."/$fileName.pdf");
    }

    public function seenVideos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->errors()->first(),
            ]);
        }

        $data = [
            'user_id'           => $request->user()->id,
            'chapter_video_id'  => $request->get('video_id'),
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $insert = UserVideo::insert($data);
        if ($insert) {
            return response()->json([
                'success'          => true,
                'message'          => "Video seen complete",
            ]);
        }
        return response()->json([
            'success'          => false,
            'message'          => "Data not found",
        ]);
    }

    public function postTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'               => 'required',
            'transaction_id'        => 'required',
            'plan'                  => 'required',
            'amount'                => 'required',
            'start_date'            => 'required',
            'end_date'              => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->errors()->first(),
            ]);
        }
        
        DB::table('transactions')->where('user_id', $request->get('user_id'))->delete();
        //'user_id'           => $request->user()->id,
        $data = [
            'user_id'           => $request->get('user_id'),
            'transaction_id'    => $request->get('transaction_id'),
            'plan'              => $request->get('plan'),
            'amount'            => $request->get('amount'),
            'start_date'        => $request->get('start_date'),
            'end_date'          => $request->get('end_date'),
            'status'            => 1,
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $insert = Transaction::insert($data);
        if ($insert) {
            return response()->json([
                'success'          => true,
                'message'          => "Your plans subscribed successfuly",
            ]);
        }
        return response()->json([
            'success'          => false,
            'message'          => "Oops! something went wrong",
        ]);
    }
    
    public function updateplan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan'                  => 'required',
            'user_id'                => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->errors()->first(),
            ]);
        }
        $end_date = date('Y-m-d H:i:s',strtotime('+28 days',strtotime(date('Y-m-d H:i:s'))));
        
        $Transaction = Transaction::where('user_id', '=', $request->get('user_id'))->first();
        if ($Transaction === null) {
           return response()->json([
                'success'          => false,
                'message'          => "Transaction not available",
            ]);
        }

        //echo "<pre>"; print_r($end_date); die;
        
        $update = Transaction::where('user_id', $request->get('user_id'))
       ->update([
           'plan' => $request->get('plan'),
           'start_date' => date('Y-m-d H:i:s'),
           'end_date' => $end_date,
        ]);
        
        
        if ($update) {
            return response()->json([
                'success'          => true,
                'message'          => "Your plans update successfuly",
            ]);
        }
        return response()->json([
            'success'          => false,
            'message'          => "Oops! something went wrong",
        ]);
    }


    public function VideosList(Request $request, $chapter_id)
    {
        $data = ChapterVideo::where(['chapter_id' => $chapter_id, 'status' => 1])
            ->select(['id', 'title', 'video', 'image_thumb', 'status', 'created_at', 'updated_at'])
            ->get();

        if (count($data) > 0) {
            return response()->json([
                'success' => true,
                'message' => "Videos list",
                'data'    => $data,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => "Data not found.",
        ]);
    }

    
}
