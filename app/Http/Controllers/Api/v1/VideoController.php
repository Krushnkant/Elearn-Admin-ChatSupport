<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Category, Course, Question, Question_option, Answer, TestResult, Ebook, Assessment,CourseVideo,Chapter,ChapterVideo};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Response, DB, Mail;

class VideoController extends Controller
{
    //
    public function videos(Request $request)
	{
	  	$query = CourseVideo::query();
	  
	  	$data = $query->where('status', 1)->get();


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

    public function coursevideosbyId(Request $request,$id)
	{
        $query = Course::query();
  
        $data = $query->where('id', $id)
                      ->with([
                            //'skill:id,name',
                              'chapters' => function($q) {
                                $q->select(['id', 'chapter', 'name', 'course_id'])
                                  ->with([
                                    'videos' => function($qq) {
                                      $qq->select(['id', 'chapter_id', 'title', 'video', 'image_thumb', 'status'])
                                        ->where('status', 1)
                                        ->with('userVideos:id,chapter_video_id,user_id');
                                    }
                                  ]);
                              },
                            ])
                      ->orderBy('created_at', 'desc')
                      ->first();
        if($data) {
          return response()->json([
            'success' => true,
            'message' => "Video list by course details",
            'data'    => $data,
          ]);
        }
	}

    public function videosbyId(Request $request,$id)
	{
        $query = ChapterVideo::query();
  
        $data = $query->where('id', $id)
                      ->where('status', 1)
                      ->orderBy('created_at', 'desc')
                      ->first();
        if($data) {
          return response()->json([
            'success' => true,
            'message' => "Video Details",
            'data'    => $data,
          ]);
        }
	}
}
