<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Category, Course, Question, Question_option, Answer, TestResult, Ebook, Assessment, HubCard};
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

	  	// Admin-managed "PMP Learning Hub" cards shown on the dashboard.
	  	$data['hubCards'] = HubCard::where('status', 1)
	  		->orderBy('sort_order', 'asc')
	  		->get(['title', 'description', 'icon', 'color', 'link_url', 'is_new']);

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

		$distinctSolved = (int) (clone $base)->distinct('answers.question_id')->count('answers.question_id');

		// Exam readiness is a correctness rate, so it must only look at answers
		// that were actually graded. Legacy rows from before correctness
		// tracking existed have `is_correctans` left NULL — counting those as
		// "attempted" here (while they can never satisfy `> 0`) silently
		// treats every one of them as wrong, crushing the readiness score.
		$graded    = (clone $base)->whereNotNull('answers.is_correctans');
		$attempted = (int) (clone $graded)->count();
		$correct   = (int) (clone $graded)->where('answers.is_correctans', '>', 0)->count();

		$questionPct = $totalQ > 0 ? ($distinctSolved / $totalQ * 100) : 0;
		$videoPct    = $this->videoWatchPercent($userId);

		// "Overall Progress" covers the whole PMP Learning Hub, not just mock
		// tests — a learner who has only been watching course/live videos
		// (never yet solved a question) previously always read as 0%. Blend
		// the two halves of the hub evenly; a single-source student still
		// shows real movement instead of being capped at half by definition.
		$overall = $totalQ > 0 || $videoPct > 0
			? (int) round(($questionPct + $videoPct) / 2)
			: 0;

		return [
			'overall_progress' => $overall,
			'mock_tests_taken' => $mockTestsTaken,
			'questions_solved' => $distinctSolved,
			'questions_total'  => $totalQ,
			'exam_readiness'   => $attempted > 0 ? (int) round($correct / $attempted * 100) : 0,
		];
	}

	/**
	 * % of all active chapter + live-session videos this user has watched
	 * (marked seen), across both video features.
	 */
	private function videoWatchPercent($userId)
	{
		$totalChapterVideos = (int) DB::table('chapter_videos')->where('status', 1)->count();
		$totalLiveVideos    = (int) DB::table('live_video_links')->where('status', 1)->count();
		$totalVideos        = $totalChapterVideos + $totalLiveVideos;

		if ($totalVideos === 0) {
			return 0;
		}

		$seenChapterVideos = (int) DB::table('users_videos')
			->where('user_id', $userId)
			->distinct('chapter_video_id')
			->count('chapter_video_id');

		$seenLiveVideos = (int) DB::table('live_video_seens')
			->where('user_id', $userId)
			->distinct('live_video_link_id')
			->count('live_video_link_id');

		return ($seenChapterVideos + $seenLiveVideos) / $totalVideos * 100;
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
