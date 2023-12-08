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

        $fields = ['id', 'mock_test_id', 'question_id', 'answer_id'];
        DB::enableQueryLog(); // Enable query log
        $data = Answer::where('mock_test_id', $id)
            ->select($fields)
            ->withCount(['answer as is_correct' => function ($query) {
                $query->where('is_correct', 1);
            }])
            ->with([

                'question:id,category_id,categoryid,sub_category_id',
            ])
            ->get();
         //dd($data->toArray());
        //  dd($data->toArray());
        //    $queries =DB::getQueryLog();
        //     dd($queries);
		
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

        $q_ids = Answer::where('mock_test_id', $id)->pluck('question_id');
        
		echo "<pre>"; print_r();die;

        /*$group['domain_count'] = Question::whereIn('id', $q_ids)
                                ->where('category_id', 2)
                                ->count();

        $group['domain_correct'] = Answer::where('mock_test_id', $id)
                                            ->whereIn('question_id', $q_ids)
                                            ->select($fields)
                                            ->withCount(['answer as is_correct' => function($query) {
                                                $query->where('is_correct', 1);
                                            }]);
                                            //->count();*/

        $is_correct = 0;
        foreach ($data as $key => $value) {
            $is_correct = $value->is_correct == Answer::CORRECT ? $is_correct + 1 : $is_correct;
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
                'pdf_download'    =>"https://chatsupport.co.in/public/storage/pdf/$fileName.pdf",
                "categoryWiseReport" => [
                    [
                        "title" => "Domain",
                        "totalQuestion" => $allDomainAnswers->count(),
                        "correctQuestion" => $domainAnswers->count(),
                        "scored" => $this->getPercentage(
                            $domainAnswers->count(),
                            $allDomainAnswers->count()
                        ),
                        "category" => $allDomainCategory->values()
                    ],
                    [
                        "title" => "Knowledge",
                        "totalQuestion" => $allKnowledgeAnswers->count(),
                        "correctQuestion" => $knowledgeAnswers->count(),
                        "scored" => $this->getPercentage(
                            $knowledgeAnswers->count(),
                            $allKnowledgeAnswers->count()
                        ),
                        "category" => $allKnowledgeCategory->values()
                    ],
                    [
                        "title" => "Approch",
                        "totalQuestion" => $allApprochAnswers->count(),
                        "correctQuestion" => $approchAnswers->count(),
                        "scored" => $this->getPercentage(
                            $approchAnswers->count(),
                            $allApprochAnswers->count()
                        ),
                        "category" => $allApprochCategory->values()
                    ]
                ]
            ];

            $this->pdf($fileName,$response);
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

        $data = [
            'user_id'           => $request->user()->id,
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
