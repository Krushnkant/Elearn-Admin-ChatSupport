<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Category, Course, Question, Question_option, Answer, TestResult, Ebook, Assessment};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Validator;
use Response, DB, Mail, Auth;


class MockTestController extends Controller
{
	public function index(Request $request, $course_id)
    {
        $data = MockTest::where(['course_id' => $course_id, 'status' => 1])
                            ->select(['id', 'title'])
                            ->get();

        if(count($data) > 0) {
          return response()->json([
            'success' => true,
            'message' => "Assessment list",
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
      $query = MockTest::query();
        $data = MockTest::where('status', 1)
                            ->select(['id', 'title'])
                            ->get();

        if(count($data) > 0) {
          return response()->json([
            'success' => true,
            'message' => "Mocktest List",
            'data'    => $data,
          ]);
        }
        return response()->json([
          'success' => false,
          'message' => "Data not found.",
        ]);
    }

    public function mocktestTotal(Request $request)
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
	  		->get(['id', 'title', 'image', 'ebook', 'created_at']);

	  	$data['videoCources'] = Course::with(['skill:id,name', 'assessments:id,course_id,title'])
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
  

}
