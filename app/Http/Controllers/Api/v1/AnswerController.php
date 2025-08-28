<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Category, Question, Question_option, Answer, TestResult , TestRemaining};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Response, DB, Mail;
use Log;

class AnswerController extends Controller
{
	public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id'     => 'required',
            'assessment_id' => 'required',
            'my_answers'    => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'message'   => $validator->errors()->first(),
            ]);
        }

        \DB::beginTransaction();
        try {
            $param = [
                'course_id'     => $request->course_id,
                'assessment_id' => $request->assessment_id,
                'user_id'       => $request->user()->id,
                'created_at'    => now(),
            ];
            $last_id = TestResult::insertGetId($param);

            $test_remaining = DB::table('test_remaining')
                ->where('assessment_id',$request->assessment_id)
                ->where('user_id',$request->user()->id)
                ->first();

            $data = [];
            $addedQuestions = []; // track already inserted question_ids

            // 1. Test Remaining Questions
            if(isset($test_remaining)) {
                $remaining_questions = DB::table('test_remaining_questions')
                    ->where('test_remaining_id', $test_remaining->id)
                    ->get();

                foreach($remaining_questions as $rq){
                    if(!in_array($rq->question_id, $addedQuestions)) { // ✅ prevent duplicate
                        $data[] = [
                            'mock_test_id'  => $last_id,
                            'question_id'   => $rq->question_id,
                            'answer_id'     => $rq->answer_id,
                            'is_correctans' => $rq->currect_answer_id,
                            'created_at'    => now(),
                        ];
                        $addedQuestions[] = $rq->question_id;
                    }
                }
            }

            // 2. User answers
            $userAnswers = [];
            if($request->get('my_answers')) {
                $my_answers  = json_decode($request->get('my_answers'));

                foreach($my_answers as $value){
                    $is_correct = 1;
                    if($value->myAnswerId != 0){
                        $ansarray = explode(',', $value->myAnswerId); 
                        $currectansarray = explode(',', $value->currectAns);
                        $resultarray = array_diff($ansarray,$currectansarray);

                        if(sizeof($resultarray) > 0){
                            $is_correct = 0; 
                        }
                    } else {
                        $is_correct = 2; // skipped
                    }

                    if(!in_array($value->questionId, $addedQuestions)) { // ✅ prevent duplicate
                        $data[] = [
                            'mock_test_id'  => $last_id,
                            'question_id'   => $value->questionId,
                            'answer_id'     => $value->myAnswerId,
                            'is_correctans' => $is_correct,
                            'created_at'    => now(),
                        ];
                        $addedQuestions[] = $value->questionId;
                    }
                    $userAnswers[] = $value->questionId;
                }
            }

            // 3. Default entry for skipped questions
            if(isset($test_remaining)) {
                foreach($remaining_questions as $rq){
                    if(!in_array($rq->question_id, $userAnswers) && !in_array($rq->question_id, $addedQuestions)) {
                        $data[] = [
                            'mock_test_id'  => $last_id,
                            'question_id'   => $rq->question_id,
                            'answer_id'     => 0,
                            'is_correctans' => 2, // skipped
                            'created_at'    => now(),
                        ];
                        $addedQuestions[] = $rq->question_id;
                    }
                }
            }

            // ✅ Now insert only unique data
            $result = Answer::insert($data);

            \DB::commit();
            if($result) {
                $user_id = Auth::id();
                $test_remaining = DB::table('test_remaining')
                    ->where('assessment_id',$request->assessment_id)
                    ->where('user_id',$user_id)
                    ->first();
                
                if(isset($test_remaining->id)){
                    DB::table('test_remaining_questions')->where('test_remaining_id', $test_remaining->id)->delete();
                    DB::table('test_remaining')->where('id', $test_remaining->id)->delete();
                }

                return response()->json([
                    "success"       => true,
                    "message"       => "Answer submitted successfully",
                    "mock_test_id"  => $last_id
                ]);
            }
            return response()->json([
                "success" => false,
                "message" => "Oops! something went wrong"
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function store1(Request $request)
    {

       $validator = Validator::make($request->all(), [
             'course_id'     => 'required',
             'assessment_id' => 'required',
             'my_answers'   => 'required',
            //'questionId'   => 'required|array',
             //'myAnswerId'     => 'required|array'
         ]);

         Log::info($request->all());
    
        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }
        //dd(json_decode($request->my_answers));
        //dd($data);
        \DB::beginTransaction();
        try {

            $TestRemaining = TestRemaining::where('set',$request->set)->where('user_id',$request->user_id)->where('assessment_id',$request->assessment_id)->orderBy('id', 'desc')->first();
          
            if($TestRemaining == ""){
            $param = [
                'course_id' => $request->course_id,
                'assessment_id' => $request->assessment_id,
                'user_id'       => $request->user_id,
                'stop_time'   => $request->stop_time,
                'question_no'   => $request->question_no,
                'set'   => intval(preg_replace('/\D/', '', $request->set)), // ✅ fix
                'created_at'    => date('Y-m-d H:i:s'),
            ];
            //dd($param);
            $last_id = TestRemaining::insertGetId($param);

            if($request->get('my_answers')) {
                $my_answers  = json_decode($request->get('my_answers'));
                $question_id  = $request->get('questionId');
                $answer_id    = $request->get('myAnswerId');
                //dd($question_id);
                foreach($my_answers as $key => $value){
                      $is_correct = 1;
                    if($value->myAnswerId != 0){
                        $ansarray = explode(',', $value->myAnswerId); 
                       $currectansarray = explode(',', $value->currectAns);
                       $resultarray = array_diff($ansarray,$currectansarray);
                     
                        if(sizeof($resultarray) > 0){
                            $is_correct = 0; 
                        }

                    }else{
                        $is_correct = 2;
                    }
                   
                    $data[] = [
                        'test_remaining_id'  => $last_id,
                        'question_id'   => $value->questionId,
                        'answer_id'     => $value->myAnswerId,
                        'currect_answer_id' => $value->currectAns,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ];
                }
            }

            $result = \DB::table('test_remaining_questions')->insert($data);
            }else{

                 $param = [
                   'course_id' => $request->course_id,
                    'assessment_id' => $request->assessment_id,
                    'user_id'       => $request->user_id,
                    'stop_time'   => $request->stop_time,
                    'question_no'   => $request->question_no,
                    'set'   => intval(preg_replace('/\D/', '', $request->set)), // ✅ fix
                ];
                $update_id = \DB::table('test_remaining')->where('id', $TestRemaining->id)->limit(1)->update($param);

                  if($request->get('my_answers')) {
                    $my_answers  = json_decode($request->get('my_answers'));
                    $question_id  = $request->get('questionId');
                    $answer_id    = $request->get('myAnswerId');
                    //dd($question_id);
                    foreach($my_answers as $key => $value){
                        $is_correct = 1;
                        if($value->myAnswerId != 0){
                            $ansarray = explode(',', $value->myAnswerId); 
                        $currectansarray = explode(',', $value->currectAns);
                        $resultarray = array_diff($ansarray,$currectansarray);
                        
                            if(sizeof($resultarray) > 0){
                                $is_correct = 0; 
                            }

                        }else{
                            $is_correct = 2;
                        }
                    
                        $data[] = [
                            'test_remaining_id'  => $TestRemaining->id,
                            'question_id'   => $value->questionId,
                            'answer_id'     => $value->myAnswerId,
                            'currect_answer_id' => $value->currectAns,
                            'created_at'    => date('Y-m-d H:i:s'),
                        ];
                    }
                }

                 $result = \DB::table('test_remaining_questions')->insert($data);

              $last_id = $TestRemaining->id;
            }

            //$result = Answer::insert($data);
            \DB::commit();
            
            return response()->json([
                "success"       => true,
                "message"       => "Answer submited successfully",
                "test_remaining_id"  => $last_id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Oops! something went wrong"
            ]);
        } catch (\Exception $e) {
            
        
        Log::info($e);
            throw $e;
            \DB::rollback();
        }

    }

    public function store2(Request $request)
    {
       // return $request->all();
       $validator = Validator::make($request->all(), [
             'test_remaining_id'     => 'required',
             //'my_answers'   => 'required',
            //'questionId'   => 'required|array',
             //'myAnswerId'     => 'required|array'
         ]);

    
        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }

        $user_id =Auth::id();
        //dd(json_decode($request->my_answers));
        //dd($data);
        \DB::beginTransaction();
        try {
            $param = [
                'stop_time' => $request->count,
                'question_no' => $request->question_no,
                'correct_ans' => $request->correct_ans,
                'last_q_id' => $request->queId,
                
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            //dd($param);
            //$last_id = TestRemaining::insertGetId($param);
            $update_id = \DB::table('test_remaining')->where('id', $request->test_remaining_id)->limit(1)->update($param);

            $param1 = [
                'answer_id' => $request->ansId,
                'currect_answer_id' => $request->correctans,
                'updated_at'    => date('Y-m-d H:i:s'),
            ];

            
            $update_id1 = \DB::table('test_remaining_questions')->where('test_remaining_id', $request->test_remaining_id)->where('question_id', $request->queId)->limit(1)->update($param1); 

            
            
            \DB::commit();
            if($update_id1){
                return response()->json([
                    "success"       => true,
                    "message"       => "Answer submited successfully",
                    "test_remaining_id"  => $update_id
                ]);
            }
            return response()->json([
                "success" => false,
                "message" => "Oops! something went wrong"
            ]);
        } catch (\Exception $e) {
            throw $e;
            \DB::rollback();
        }

    }

}
