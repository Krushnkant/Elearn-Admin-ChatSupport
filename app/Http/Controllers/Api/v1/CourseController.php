<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Course,Coursevideo};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Response, DB, Mail, Auth;


class CourseController extends Controller
{
	public function index(Request $request)
	{
  	$query = Course::query();
	
  	$data = $query->with(['skill:id,name'])->orderBy('created_at', 'desc')
                    ->paginate($request->get('per_page') ? $request->get('per_page') : 30);
  	if(count($data) > 0) {
      return response()->json([
        'success' => true,
        'message' => "course List.",
        'data'    => $data,
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
	}

  public function show($id)
  {
    $query = Course::query();
  
    $data = $query->where('id', $id)
                  ->with([
                          'skill:id,name',
                          'chapters' => function($q) {
                            $q->select(['id', 'chapter', 'name', 'course_id'])
                              ->with([
                                'videos' => function($qq) {
                                  $qq->select(['id', 'chapter_id', 'title', 'description' ,'video', 'image_thumb', 'status'])
                                    ->where('status', 1)
                                    ->with('userVideos:id,chapter_video_id,user_id');
                                }
                              ]);
                          },
                          'assessments' => function($qq) {
                            $qq->select(['id', 'course_id', 'title', 'status'])
                              ->with(['questions.category']);
                          },
                          'books' => function($qq) {
                            $qq->where('status', 1)->select(['id', 'course_id', 'title', 'preview as image', 'book as ebook']);
                          }
                          /*'assessments' => function($qq) {
                            $qq->select(['id', 'category_id', 'sub_category_id', 'course_id', 'title', 'question_type', 'marks', 'dificulty_level', 'explanation', 'status', 'assessment_id'])->with(['category:id,name,type,status', 'questionOptions']);
                          }*/
                        ])
                  ->orderBy('created_at', 'desc')
                  ->first();
				  
	//echo "<pre>"; print_r($data);die;
    if($data) {
      return response()->json([
        'success' => true,
        'message' => "course Details",
        'data'    => $data,
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
  }


  public function coursesGetById(Request $request,$id)
	{
  	$query = Course::query();
  
  	$data = $query->where('id',$id)->select(['id','name','created_at','trainnig_mode','image','overview','mock_test','course_outline','mock_exam_count'])->get();
  	if(count($data) > 0) {
      return response()->json([
        'success' => true,
        'message' => "course overview.",
        'data'    => $data,
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
	}

  public function coursesOutline(Request $request,$id)
	{
  	$query = Course::query();
  
  	$data = $query->where('id',$id)->select(['id','name','created_at','trainnig_mode','image','overview','mock_test','course_outline','mock_exam_count'])->get();
  	if(count($data) > 0) {
      return response()->json([
        'success' => true,
        'message' => "course outline.",
        'data'    => $data,
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
	}

  public function coursevideosbyId(Request $request,$id)
	{
  	 $query = Course::query();
  
    $data = $query->where('id', $id)
                  ->with([
                          'books' => function($qq) {
                            $qq->where('status', 1)->select(['id', 'course_id', 'title', 'preview', 'book']);
                          },
                        
                        ])
                  ->orderBy('created_at', 'desc')
                  ->first();

  if($data)  {
      return response()->json([
        'success' => true,
        'message' => "course book List.",
        'data'    => $data,
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
	}
}
