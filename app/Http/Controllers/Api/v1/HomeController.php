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
