<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use App\Models\{Answer,TestResult};
use Illuminate\Support\Facades\File;
use DataTables ,Validator, Session, Redirect, Response, DB, Config;

class UserResultController extends Controller
{
  public function index(Request $request, $user_id)
  {
    $data['title'] = "User Result List";
    $data['user_id'] = $user_id;

    if ($request->ajax())
    {
        $data = TestResult::where('user_id', decode($user_id))->orderBy('id', 'desc');
                  return Datatables::of($data)
                  ->editColumn('created_at', function($data){
                    return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
                  })
                  //->addColumn('action', 'admin.datatable.action.user.result-action')
                  //->editColumn('status', 'admin.datatable.status.status')
                  //->rawColumns(['status'])
                  ->addIndexColumn()
                  ->make(true);
    }

    return view('admin.user-result.list', $data);
  }

  public function create(Request $request, $user_id)
  {
    $data['title'] = "Create Chapter";
    $data['user_id'] = $user_id;
    return view('admin.chapter.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name'        => 'required',
      'chapter'     => 'required',
    ]);

    $data = [
      'name'        => $request->get('name'),
      'course_id'   => decode($request->get('course_id')),
      'chapter'     => $request->get('chapter'),
      'status'      => $request->get('status') == 1 ? 1 : 0,
      'created_at'  => date('Y-m-d H:i:s'),
    ];
    
    $insert_id = Chapter::insertGetId($data);
    
    if($insert_id) {
      return Redirect::to("admin/courses/".$request->get('course_id')."/chapters")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/courses/".$request->get('course_id')."/chapters/create")->withWarning("Oops! Something went wrong");
    }
  }

  public function edit(Request $request, $course_id, $id)
  {
    $data['title'] = "Edit Course";
    $data['course_id'] = $course_id;
    $data['id'] = $id;
    $data['chapter_info'] = Chapter::where(['course_id' => decode($course_id), 'id' => decode($id)])->first();
    return view('admin.chapter.edit', $data);
  }

  public function update(Request $request)
  {
    //dd($request->all());
    $id = $request->get('id');
    $request->validate([
      'name'        => 'required',
      'chapter'     => 'required',
    ]);

    $data = [
      'name'        => $request->get('name'),
      'course_id'   => decode($request->get('course_id')),
      'chapter'     => $request->get('chapter'),
      'status'      => $request->get('status') == 1 ? 1 : 0,
      'created_at'  => date('Y-m-d H:i:s'),
    ];
    $update = Chapter::where('id', decode($id))->update($data);
    if($update) {
      return Redirect::to("admin/courses/".$request->get('course_id')."/chapters")->withSuccess("Great! Info has been updated");
    } else {
      return Redirect::to("admin/courses/".$request->get('course_id')."/chapters/".$request->get('id')."edit")->withWarning("Oops! Something went wrong");
    }
  }
}

