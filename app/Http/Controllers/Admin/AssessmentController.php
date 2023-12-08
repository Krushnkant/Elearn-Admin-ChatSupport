<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use App\Models\{Assessment, Skill};
use Illuminate\Support\Facades\File;
use DataTables ,Validator, Session, Redirect, Response, DB, Config;

class AssessmentController extends Controller
{
  public function index(Request $request, $course_id)
  {
    $data['title'] = "Assessment List";
    $data['course_id'] = $course_id;
    if ($request->ajax())
    {
        $data = Assessment::where('course_id', decode($course_id))->with('course')->orderBy('id', 'desc');
                  return Datatables::of($data)
                  ->editColumn('created_at', function($data){
                    return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
                  })
                  ->addColumn('action', 'admin.datatable.action.assessment.assessment-action')
                  ->editColumn('status', 'admin.datatable.status.status')
                  ->rawColumns(['status', 'action'])
                  ->addIndexColumn()
                  ->make(true);
    }

    return view('admin.assessments.list', $data);
  }

  public function create(Request $request, $course_id)
  {
    $data['title'] = "Create Assessment";
    $data['course_id'] = $course_id;
    $data['skill_info'] = Skill::where('status', 1)->get();
    return view('admin.assessments.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'title'               => 'required',
      'skill_id'            => 'required',
      'number_of_questions' => 'required',
      'mock_exam'           => 'required',
      'description'         => 'required',
    ],
    [
      'skill_id.required' => " The skill field is required."
    ]
  );

    $data = [
      'title'               => $request->get('title'),
      'skill_id'            => $request->get('skill_id'),
      'number_of_questions' => $request->get('number_of_questions'),
      'mock_exam'           => $request->get('mock_exam'),
      'description'         => $request->get('description'),
      'course_id'           => decode($request->get('course_id')),
      'status'              => $request->get('status') == 1 ? 1 : 0,
      'created_at'          => date('Y-m-d H:i:s'),
    ];
    
    if($request->hasFile('image')) {
      $files = $request->file('image');
      $destinationPath = 'public/assessments/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image'] = $profileImage;
    }
    $insert_id = Assessment::insertGetId($data);
    
    if($insert_id) {
      return Redirect::to("admin/courses/".$request->get('course_id')."/assessments")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/courses/".$request->get('course_id')."/assessments/create")->withWarning("Oops! Something went wrong");
    }
  }

  public function edit(Request $request, $course_id, $id)
  {
    $data['title'] = "Edit Assessment";
    $data['course_id'] = $course_id;
    $data['id'] = $id;
    $data['skill_info'] = Skill::where('status', 1)->get();
    $data['assessment_info'] = Assessment::where(['course_id' => decode($course_id), 'id' => decode($id)])->first();
    return view('admin.assessments.edit', $data);
  }

  public function update(Request $request)
  {
    //dd($request->all());
    $id = $request->get('id');
    $request->validate([
      'title'               => 'required',
      'skill_id'            => 'required',
      'number_of_questions' => 'required',
      'mock_exam'           => 'required',
      'description'         => 'required',
    ],
    [
      'skill_id.required' => " The skill field is required."
    ]
  );

    $data = [
      'title'               => $request->get('title'),
      'skill_id'            => $request->get('skill_id'),
      'number_of_questions' => $request->get('number_of_questions'),
      'mock_exam'           => $request->get('mock_exam'),
      'description'         => $request->get('description'),
      'status'              => $request->get('status') == 1 ? 1 : 0,
      'updated_at'          => date('Y-m-d H:i:s'),
    ];

    if($request->hasFile('image')) {
      $image = Assessment::where('id', decode($id))->select(['image'])->first();
      $d_file = 'public/assessments/' . $image['originalImage'];
      File::delete($d_file);
      $files = $request->file('image');
      $destinationPath = 'public/assessments/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image'] = $profileImage;
    }

    $update = Assessment::where('id', decode($id))->update($data);
    if($update) {
      return Redirect::to("admin/courses/".$request->get('course_id')."/assessments")->withSuccess("Great! Info has been updated");
    } else {
      return Redirect::to("admin/courses/".$request->get('course_id')."/assessments/".$request->get('id')."edit")->withWarning("Oops! Something went wrong");
    }
  }
}

