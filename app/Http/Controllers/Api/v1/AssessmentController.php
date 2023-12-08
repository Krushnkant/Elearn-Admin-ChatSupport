<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Assessment};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Validator;
use Response, DB, Mail, Auth;


class AssessmentController extends Controller
{
	public function index(Request $request, $course_id)
  {
      $data = Assessment::where(['course_id' => $course_id, 'status' => 1])
                          ->select(['id', 'title', 'number_of_questions', 'skill_id', 'mock_exam', 'image'])
                          ->with('skill:id,name')
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

  public function mockTest(Request $request)
  {
    $data = Assessment::where('status', 1)
                        ->select(['id', 'title', 'number_of_questions', 'skill_id', 'mock_exam', 'image', 'description'])
                        ->with('skill:id,name')
                        ->paginate(50);

    if(count($data) > 0) {
      return response()->json([
        'success' => true,
        'message' => "Mock test list",
        'data'    => $data->items(),
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
  }

  public function mockTestTest(Request $request)
  {
    $user_info = $request->user()->activeMembership();

    $plan = isset($user_info->plan)? $user_info->plan : 0;
    
    $data = Assessment::where('status', 1)->select([
      'id', 'title', 'number_of_questions', 'skill_id', 'mock_exam', 'image', 'description', 
      \DB::raw("'{$plan}' as sets")
    ])
    ->with('skill:id,name')
    ->paginate(50);


    if(count($data) > 0) {
      return response()->json([
        'success' => true,
        'message' => "Mock test list",
        'data'    => $data->items()
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
  }

}
