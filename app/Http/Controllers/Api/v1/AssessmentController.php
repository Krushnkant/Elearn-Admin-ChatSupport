<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Assessment, ContentSetting};
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

  /**
   * Mock Tests tab header copy, managed from Admin > Content Management.
   * Missing values come back null and the app falls back to its defaults.
   */
  private function mockTestHeader(): array
  {
    $c = ContentSetting::group('mock_test');
    return [
      'title'       => $c['title'] ?? null,
      'badges'      => [
        $c['badge_1'] ?? null,
        $c['badge_2'] ?? null,
        $c['badge_3'] ?? null,
        $c['badge_4'] ?? null,
      ],
      'description' => $c['description'] ?? null,
    ];
  }

  public function mockTest(Request $request)
  {
    $userId = optional($request->user())->id;

    // Curated tests for everyone, plus this user's own builder-generated
    // tests — a custom test belongs to exactly one user and must never leak
    // into another user's list.
    $data = Assessment::where('status', 1)
                        ->where(function ($q) use ($userId) {
                          $q->where(function ($q2) {
                            $q2->where('is_custom', 0)->orWhereNull('is_custom');
                          });
                          if ($userId) {
                            $q->orWhere(function ($q2) use ($userId) {
                              $q2->where('is_custom', 1)->where('user_id', $userId);
                            });
                          }
                        })
                        ->select(['id', 'title', 'number_of_questions', 'skill_id', 'mock_exam', 'image', 'description', 'duration_mins'])
                        ->with('skill:id,name')
                        ->paginate(50);

    // Per-test attempt status for the logged-in student, so the app can
    // show "Attempted"/"Not Attempted" instead of a hardcoded label.
    $attemptCounts = [];
    if ($userId) {
      $attemptCounts = DB::table('test_results')
          ->select('assessment_id', DB::raw('count(*) as c'))
          ->where('user_id', $userId)
          ->whereIn('assessment_id', collect($data->items())->pluck('id'))
          ->groupBy('assessment_id')
          ->pluck('c', 'assessment_id');
    }

    $items = collect($data->items())->map(function ($assessment) use ($attemptCounts) {
      $assessment->attempts_count = (int) ($attemptCounts[$assessment->id] ?? 0);
      return $assessment;
    });

    if(count($data) > 0) {
      return response()->json([
        'success' => true,
        'message' => "Mock test list",
        'data'    => $items,
        'header'  => $this->mockTestHeader(),
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
