<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use App\Models\{CourseVideo};
use Illuminate\Support\Facades\File;
use DataTables ,Validator, Session, Redirect, Response, DB, Config;

class BookController extends Controller
{
  public function index(Request $request, $course_id)
  {
    $data['title'] = "Book List";
    $data['course_id'] = $course_id;

    if ($request->ajax())
    {
        $data = CourseVideo::where('course_id', decode($course_id))->orderBy('id', 'desc');
        
        return Datatables::of($data)
            ->editColumn('created_at', function($data){
              return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
            })
            ->addColumn('action', 'admin.datatable.action.book.book-action')
            ->editColumn('status', 'admin.datatable.status.status')
            ->rawColumns(['status', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('admin.book.list', $data);
  }

  public function create(Request $request, $course_id)
  {
    $data['title'] = "Add Book";
    $data['course_id'] = $course_id;
    return view('admin.book.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'title'        => 'required',
    ]);

    $data = [
      'title'        => $request->get('title'),
      'course_id'   => decode($request->get('course_id')),
      'status'      => $request->get('status') == 1 ? 1 : 0,
      'created_at'  => date('Y-m-d H:i:s'),
    ];

    if($request->hasFile('image')) {
      $files = $request->file('image');
      $destinationPath = 'public/course_video/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['preview'] = $profileImage;
    }

    if($request->hasFile('ebook')) {
      $files = $request->file('ebook');
      $destinationPath = 'public/course_video/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['book'] = $profileImage;
    }
    
    $insert_id = CourseVideo::insertGetId($data);
    
    if($insert_id) {
      return Redirect::to("admin/courses/".$request->get('course_id')."/books")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/courses/".$request->get('course_id')."/books/create")
        ->withWarning("Oops! Something went wrong");
    }
  }

  public function edit(Request $request, $course_id, $id)
  {
    $data['title'] = "Edit Book";
    $data['course_id'] = $course_id;
    $data['id'] = $id;
    $data['info'] = CourseVideo::where(['course_id' => decode($course_id), 'id' => decode($id)])->first();
    return view('admin.book.edit', $data);
  }

  public function update(Request $request)
  {
    
    $id = $request->get('book_id');
    $course_id = $request->get('course_id');
    $book = CourseVideo::where('id', $id)->select(['preview', 'book'])->first();

    $request->validate([
      'title'        => 'required',
    ]);

    $data = [
      'title'       => $request->get('title'),
      'status'      => $request->get('status') == 1 ? 1 : 0,
    ];

    if($request->hasFile('image')) {
      $d_file = 'public/ebooks/' . $book->originalPreview;
      File::delete($d_file);
      $files = $request->file('image');
      $destinationPath = 'public/course_video/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['preview'] = $profileImage;
    }

    if($request->hasFile('ebook')) {
      $d_file = 'public/course_video/' . $book->originalBook;
      File::delete($d_file);
      $files = $request->file('ebook');
      $destinationPath = 'public/course_video/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['book'] = $profileImage;
    }


    $update = CourseVideo::where('id', $id)->update($data);
    if($update) {
      return Redirect::to("admin/courses/".encode($course_id)."/books")
        ->withSuccess("Great! Info has been updated");
    } else {
      return Redirect::to("admin/courses/".encode($course_id)."/books/". encode($id)."/edit")->withWarning("Oops! Something went wrong");
    }
  }
}

