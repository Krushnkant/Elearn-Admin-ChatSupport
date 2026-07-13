<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\StudyMaterial;
use App\Models\StudyMaterialLesson;
use DataTables;

class StudyMaterialLessonController extends Controller
{
  private $uploadPath = 'public/study_materials/';

  public function index(Request $request, $bookId)
  {
    $book = StudyMaterial::findOrFail($bookId);
    $data['title'] = "Lessons — {$book->title}";
    $data['book']  = $book;

    if ($request->ajax())
    {
      $rows = StudyMaterialLesson::where('study_material_id', $bookId)->orderBy('sort_order', 'asc');

      return Datatables::of($rows)
        ->editColumn('created_at', function($row){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($row->created_at));
        })
        ->addColumn('type', function($row){
          if ($row->file)    return '<span class="badge badge-danger">PDF</span>';
          if ($row->content) return '<span class="badge badge-info">Text</span>';
          return '<span class="badge badge-secondary">Empty</span>';
        })
        ->addColumn('action', function($row) use ($bookId) {
          return view('admin.studymateriallesson.action', ['id' => $row->id, 'bookId' => $bookId])->render();
        })
        ->editColumn('status', 'admin.datatable.status.status')
        ->rawColumns(['status', 'action', 'type'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.studymateriallesson.list', $data);
  }

  public function create(Request $request, $bookId)
  {
    $data['title'] = "Add Lesson";
    $data['book']  = StudyMaterial::findOrFail($bookId);
    return view('admin.studymateriallesson.add', $data);
  }

  public function store(Request $request, $bookId)
  {
    StudyMaterial::findOrFail($bookId);
    $request->validate(['title' => 'required']);

    $data = [
      'study_material_id' => $bookId,
      'title'             => $request->get('title'),
      'content'           => $request->get('content'),
      'sort_order'        => (int) $request->get('sort_order', 0),
      'status'            => $request->get('status'),
      'created_at'        => date('Y-m-d H:i:s'),
    ];

    if ($request->hasFile('file')) {
      $data['file'] = $this->uploadFile($request->file('file'));
    }

    StudyMaterialLesson::create($data);

    return Redirect::to("admin/study-materials/{$bookId}/lessons")->withSuccess("Great! Lesson has been added");
  }

  public function edit(Request $request, $bookId, $id)
  {
    $data['title']       = "Edit Lesson";
    $data['book']        = StudyMaterial::findOrFail($bookId);
    $data['lesson_info'] = StudyMaterialLesson::where('id', $id)->where('study_material_id', $bookId)->firstOrFail();
    return view('admin.studymateriallesson.edit', $data);
  }

  public function update(Request $request, $bookId)
  {
    $id = $request->get('id');
    $request->validate(['title' => 'required']);

    $lesson = StudyMaterialLesson::where('id', $id)->where('study_material_id', $bookId)->firstOrFail();

    $data = [
      'title'      => $request->get('title'),
      'content'    => $request->get('content'),
      'sort_order' => (int) $request->get('sort_order', 0),
      'status'     => $request->get('status'),
      'updated_at' => date('Y-m-d H:i:s'),
    ];

    if ($request->hasFile('file')) {
      // Uploading a new PDF replaces the old one.
      if ($lesson->file) { File::delete($this->uploadPath . $lesson->file); }
      $data['file'] = $this->uploadFile($request->file('file'));
    } elseif ($request->get('remove_file') == 1) {
      // Remove the PDF so the lesson falls back to its text content.
      if ($lesson->file) { File::delete($this->uploadPath . $lesson->file); }
      $data['file'] = null;
    }

    $lesson->update($data);

    return Redirect::to("admin/study-materials/{$bookId}/lessons")->withSuccess("Great! Lesson has been updated");
  }

  private function uploadFile($file)
  {
    $name = md5(time() . mt_rand(100000000, 999999999)) . '.' . $file->getClientOriginalExtension();
    $file->move($this->uploadPath, $name);
    return $name;
  }
}
