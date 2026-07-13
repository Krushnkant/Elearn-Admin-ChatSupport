<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Category, Course, Question, Question_option, Answer, TestResult, Ebook, Assessment};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Response, DB, Mail;


class HomeController extends Controller
{
	public function index(Request $request)
	{
	  	$query = Course::query();
	  
	  	$data['continueReading'] = Course::with(['skill:id,name', 'assessments:id,course_id,title'])
			->orderBy('created_at', 'desc')
			->get();

	  	$data['myCources'] = Course::with(['skill:id,name', 'assessments:id,course_id,title'])
	  		->orderBy('created_at', 'desc')
	  		->get();

	  	$data['ebooks'] = Ebook::where('status', 1)
	  		->orderBy('created_at', 'desc')
	  		->limit(20)
	  		->get(['id', 'title','description','price', 'image', 'ebook', 'created_at']);

	  	$data['videoCources'] = Course::with(['skill:id,name', 'assessments:id,course_id,title'])
	  		->orderBy('created_at', 'desc')
	  		->get();

	  	$data['mockTest'] = Assessment::where(['status' => 1])
        	->select(['id', 'title', 'number_of_questions', 'skill_id', 'mock_exam', 'image', 'created_at'])
	  		->limit(30)
            ->get();

	  	// Per-student dashboard stats (real progress for the logged-in user).
	  	$data['stats'] = $this->userStats(optional($request->user())->id);

	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}

	/**
	 * Compute real progress stats for a student, using the app's own
	 * correctness rule (Answer::getIsCorrectAttribute): a question is
	 * "attempted" when answer_id > 0, and "correct" when is_correctans > 0.
	 */
	private function userStats($userId)
	{
		$totalQ = (int) DB::table('questions')->count();

		if (!$userId) {
			return [
				'overall_progress' => 0,
				'mock_tests_taken' => 0,
				'questions_solved' => 0,
				'questions_total'  => $totalQ,
				'exam_readiness'   => 0,
			];
		}

		$mockTestsTaken = (int) DB::table('test_results')->where('user_id', $userId)->count();

		$base = DB::table('answers')
			->join('test_results', 'answers.mock_test_id', '=', 'test_results.id')
			->where('test_results.user_id', $userId)
			->where('answers.answer_id', '>', 0);

		$attempted      = (int) (clone $base)->count();
		$correct        = (int) (clone $base)->where('answers.is_correctans', '>', 0)->count();
		$distinctSolved = (int) (clone $base)->distinct('answers.question_id')->count('answers.question_id');

		return [
			'overall_progress' => $totalQ > 0 ? (int) round($distinctSolved / $totalQ * 100) : 0,
			'mock_tests_taken' => $mockTestsTaken,
			'questions_solved' => $distinctSolved,
			'questions_total'  => $totalQ,
			'exam_readiness'   => $attempted > 0 ? (int) round($correct / $attempted * 100) : 0,
		];
	}

	public function explore(Request $request)
	{
	  	$query = Course::query();
	  
	
	  	$data['ExploreCourse'] = Course::with(['skill:id,name', 'assessments:id,course_id,title'])
	  		->orderBy('created_at', 'desc')
	  		->get();

	  	$data['ExploreEbook'] = Ebook::where('status', 1)
	  		->orderBy('created_at', 'desc')
	  		->limit(20)
	  		->get(['id', 'title', 'image', 'ebook', 'created_at']);


	  	$data['mockTest'] = Assessment::where(['status' => 1])
        	->select(['id', 'title', 'number_of_questions', 'skill_id', 'mock_exam', 'image', 'created_at'])
	  		->limit(30)
            ->get();

	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}

	public function exploreCourse(Request $request)
	{
	  	$query = Course::query();
	  
	
	  	$data['ExploreCourse'] = Course::with(['skill:id,name', 'assessments:id,course_id,title'])
	  		->orderBy('created_at', 'desc')
	  		->get();

	

	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}
	public function exploreEbook(Request $request)
	{
	  	$query = Course::query();
	  
	

	  	$data['ExploreEbook'] = Ebook::where('status', 1)
	  		->orderBy('created_at', 'desc')
	  		->limit(20)
	  		->get(['id', 'title', 'image', 'ebook', 'created_at']);


	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}



	public function courseList(Request $request)
	{
	  	$query = Course::query();
	  
	  	$data['continueReading'] = Course::with(['skill:id,name', 'assessments:id,course_id,title'])
			->orderBy('created_at', 'desc')
			->get();

	  

	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}

	public function ebookList(Request $request)
	{
	  	$query = Course::query();
	  
	  
	  	$data['ebooks'] = Ebook::where('status', 1)
	  		->orderBy('created_at', 'desc')
	  		->limit(20)
	  		->get(['id', 'title', 'image', 'ebook', 'created_at']);

	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}

	public function mocktestList(Request $request)
	{
	  	$query = Course::query();
	

	  	$data['mockTest'] = Assessment::where(['status' => 1])
        	->select(['id', 'title', 'number_of_questions', 'skill_id', 'mock_exam', 'image', 'created_at'])
	  		->limit(30)
            ->get();

	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}





}
