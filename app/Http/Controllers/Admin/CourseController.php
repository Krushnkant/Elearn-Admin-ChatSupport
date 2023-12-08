<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use App\Models\{Course, Skill};
use Illuminate\Support\Facades\File;
use DataTables ,Validator, Session, Redirect, Response, DB, Config;

class CourseController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Course List";

    if ($request->ajax())
    {
        $data = Course::with('skill')->orderBy('id', 'desc');
                  return Datatables::of($data)
                  ->editColumn('created_at', function($data){
                    return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
                  })
                  ->addColumn('action', 'admin.datatable.action.course.course-action')
                  //->editColumn('status', 'admin.datatable.status.status')
                  ->rawColumns(['action'])
                  ->addIndexColumn()
                  ->make(true);
    }

    return view('admin.course.index', $data);
  }

  public function create(Request $request)
  {
    $data['title'] = "Create Course";
    $data['skill_info'] = Skill::where('status', 1)->get();
    return view('admin.course.add', $data);
  }

  public function store(Request $request)
  {
    //dd($request->all());
    $request->validate([
      'skill_id'              => 'required',
      'name'                  => 'required',
      'duration'              => 'required',
      'trainnig_mode'         => 'required',
      'overview'              => 'required',
      'mock_test'             => 'required',
      'course_outline'        => 'required',
      'mock_exam_count'       => 'required|numeric',
    ]);

    $data = [
      'skill_id'              => $request->get('skill_id'),
      'name'                  => $request->get('name'),
      'duration'              => $request->get('duration'),
      'trainnig_mode'         => $request->get('trainnig_mode'),
      'overview'              => $request->get('overview'),
      'mock_test'             => $request->get('mock_test'),
      'course_outline'        => $request->get('course_outline'),
      'created_at'            => date('Y-m-d H:i:s'),
    ];

    if($request->hasFile('image')) {
      $files = $request->file('image');
      $destinationPath = 'public/course/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image'] = $profileImage;
    }
    //dd($data);
    $insert_id = Course::insertGetId($data);
    
    if($insert_id) {
      return Redirect::to("admin/courses")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/courses/import-csv")->withWarning("Oops! Something went wrong");
    }
  }

  public function edit(Request $request, $id)
  {
    $data['title'] = "Edit Course";
    $data['skill_info'] = Skill::where('status', 1)->get();
    $data['course_info'] = Course::where('id', $id)->first();
    return view('admin.course.edit', $data);
  }

  public function update(Request $request)
  {
    //dd($request->all());
    $id = $request->get('id');
    $request->validate([
      'skill_id'              => 'required',
      'name'                  => 'required',
      'duration'              => 'required',
      'trainnig_mode'         => 'required',
      'overview'              => 'required',
      'mock_test'             => 'required',
      'course_outline'        => 'required',
      'mock_exam_count'       => 'required|numeric',
    ]);

    $data = [
      'skill_id'              => $request->get('skill_id'),
      'name'                  => $request->get('name'),
      'duration'              => $request->get('duration'),
      'trainnig_mode'         => $request->get('trainnig_mode'),
      'overview'              => $request->get('overview'),
      'mock_test'             => $request->get('mock_test'),
      'course_outline'        => $request->get('course_outline'),
      'mock_exam_count'       => $request->get('mock_exam_count'),
      'created_at'            => date('Y-m-d H:i:s'),
    ];
    if($request->hasFile('image')) {
      $image = Course::where('id', $id)->select(['image'])->first();
      $d_file = 'public/course/' . $image->originalImage;
      File::delete($d_file);
      $files = $request->file('image');
      $destinationPath = 'public/course/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image'] = $profileImage;
    }
    //dd($data);
    $update = Course::where('id', $id)->update($data);
    
    if($update) {
      return Redirect::to("admin/courses")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/courses/".$id."edit")->withWarning("Oops! Something went wrong");
    }
  }
}

