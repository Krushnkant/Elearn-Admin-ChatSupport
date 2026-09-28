<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Course,Coursevideo, CourseRating};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Response, DB, Mail, Auth;


class CourseController extends Controller
{
	/**
	 * Every course in the catalog for the "All Courses" listing page, with
	 * the counts a course card needs (chapters/lessons, mock tests, books).
	 */
	public function allCourses(Request $request)
	{
  	$data = Course::with(['skill:id,name'])
                  ->withCount(['chapters', 'assessments', 'books'])
                  ->orderBy('created_at', 'desc')
                  ->get([
                    'id', 'skill_id', 'name', 'image', 'duration', 'trainnig_mode',
                    'overview', 'mock_exam_count', 'created_at',
                  ]);

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
                                  $qq->select(['id', 'chapter_id', 'title', 'description' ,'video', 'duration', 'image_thumb', 'status'])
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
                            // Not aliased: the model's getPreviewAttribute()/getBookAttribute()
                            // accessors (which build the real asset URL) only fire for the
                            // attribute's real column name — "preview as image" would return
                            // the bare filename instead of a working download URL.
                            $qq->where('status', 1)->select(['id', 'course_id', 'title', 'preview', 'book']);
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

  /**
   * Average rating + this learner's own rating for a course.
   */
  public function rating(Request $request, $id)
  {
    $avg   = CourseRating::where('course_id', $id)->avg('rating');
    $count = CourseRating::where('course_id', $id)->count();
    $mine  = CourseRating::where('course_id', $id)->where('user_id', $request->user()->id)->value('rating');

    return response()->json([
      'success' => true,
      'data'    => [
        'average' => $avg ? round($avg, 1) : 0,
        'count'   => $count,
        'mine'    => $mine ? (int) $mine : null,
      ],
    ]);
  }

  /**
   * Add or update this learner's 1-5 star rating for a course.
   */
  public function rate(Request $request)
  {
    $request->validate([
      'course_id' => 'required',
      'rating'    => 'required|integer|min:1|max:5',
    ]);

    CourseRating::updateOrCreate(
      ['course_id' => $request->get('course_id'), 'user_id' => $request->user()->id],
      ['rating' => $request->get('rating')]
    );

    $avg   = CourseRating::where('course_id', $request->get('course_id'))->avg('rating');
    $count = CourseRating::where('course_id', $request->get('course_id'))->count();

    return response()->json([
      'success' => true,
      'message' => 'Thanks for rating this course!',
      'data'    => [
        'average' => $avg ? round($avg, 1) : 0,
        'count'   => $count,
        'mine'    => (int) $request->get('rating'),
      ],
    ]);
  }
}
