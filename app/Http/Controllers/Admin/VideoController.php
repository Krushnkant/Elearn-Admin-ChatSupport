<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use App\Models\{Chapter, ChapterVideo};
use Illuminate\Support\Facades\File;
use DataTables ,Validator, Session, Redirect, Response, DB, Config;

class VideoController extends Controller
{
  public function index(Request $request, $chapter_id)
  {
    $data['title'] = "Videos List";
    $data['chapter_id'] = $chapter_id;

    if ($request->ajax())
    {
        $data = ChapterVideo::where('chapter_id', decode($chapter_id))
          ->with('chapter')->orderBy('id', 'desc');

        return Datatables::of($data)
          ->editColumn('created_at', function($data){
            return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
          })
          ->addColumn('action', 'admin.datatable.action.videos.videos-action')
          ->editColumn('status', 'admin.datatable.status.status')
          ->rawColumns(['status', 'action'])
          ->addIndexColumn()
          ->make(true);  
    }

    return view('admin.videos.list', $data);
  }

  public function create(Request $request, $chapter_id)
  { //echo phpinfo();die;
    $data['title'] = "Create Video";
    $data['chapter_id'] = $chapter_id;
    return view('admin.videos.add', $data);
  }

  public function store(Request $request)
  { 
    $request->validate([
      'title'   		=> 'required',
	  'description'   => 'required',
      'video'   => 'required',
    ]);
//echo "dddd"; die;
    $data = [
      'title'       => $request->get('title'),
	  'description' => $request->get('description'),
      'chapter_id'  => decode($request->get('chapter_id')),
      'status'      => $request->get('status') == 1 ? 1 : 0,
      'created_at'  => date('Y-m-d H:i:s'),
    ];
	//echo "<pre>"; print_r($_FILES);die;
    if($request->hasFile('video')) {
      $files = $request->file('video');
      $destinationPath = 'public/chapter/videos/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
	  
	  
      $data['video'] = $profileImage;
    }

    if($request->hasFile('thumbnail')) {

      $files = $request->file('thumbnail');
      $destinationPath = 'public/chapter/videos/thumbnail/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image_thumb'] = $profileImage;
    }


    $insert_id = ChapterVideo::insertGetId($data);
    
    if($insert_id) {
      return Redirect::to("admin/chapters/".$request->get('chapter_id')."/videos")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/chapters/".$request->get('chapter_id')."/videos/create")->withWarning("Oops! Something went wrong");
    }
  }

  public function edit(Request $request, $chapter_id, $id)
  {
    $data['title'] = "Edit Video";
    $data['chapter_id'] = $chapter_id;
    $data['id'] = $id;
    $data['video_info'] = ChapterVideo::where('id', decode($id))->first();
    return view('admin.videos.edit', $data);
  }

  public function update(Request $request)
  {
    //dd($request->all());
    $id = $request->get('id');
    $request->validate([
      'title' => 'required',
	  'description'   => 'required',
    ]);

    $data = [
      'title'       => $request->get('title'),
	  'description' => $request->get('description'),
      'chapter_id'  => decode($request->get('chapter_id')),
      'status'      => $request->get('status') == 1 ? 1 : 0,
      'created_at'  => date('Y-m-d H:i:s'),
    ];
    if($request->hasFile('video')) {
      $image = ChapterVideo::where('id', decode($id))->select(['video'])->first();
      //dd($image);
      $d_file = 'public/chapter/videos' . $image->originalVideo;
      File::delete($d_file);

      $files = $request->file('video');
      $destinationPath = 'public/chapter/videos/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['video'] = $profileImage;
    }

    if($request->hasFile('thumbnail')) {
      $image = ChapterVideo::where('id', decode($id))->select(['image_thumb'])->first();
      //dd($image);
      $d_file = 'public/chapter/videos/thumbnail/' . $image->original_image_thumb;
      if (file_exists($d_file)) {
        File::delete($d_file);
      }

      $files = $request->file('thumbnail');
      $destinationPath = 'public/chapter/videos/thumbnail/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image_thumb'] = $profileImage;
    }

    $update = ChapterVideo::where('id', decode($id))->update($data);
    if($update) {
      return Redirect::to("admin/chapters/".$request->get('chapter_id')."/videos")->withSuccess("Great! Info has been updated");
    } else {
      return Redirect::to("admin/chapters/".$request->get('chapter_id')."/videos/".$request->get('id')."edit")->withWarning("Oops! Something went wrong");
    }
  }
}

