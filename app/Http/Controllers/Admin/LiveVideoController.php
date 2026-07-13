<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\LiveVideo;
use DataTables;

class LiveVideoController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Live Video List";

    if ($request->ajax())
    {
      $data = LiveVideo::orderBy('day_no', 'asc');

      return Datatables::of($data)
        ->editColumn('created_at', function($data){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
        })
        ->editColumn('scheduled_at', function($data){
          return $data->scheduled_at ? date('d M Y, h:i A', strtotime($data->scheduled_at)) : '—';
        })
        ->addColumn('action', 'admin.livevideo.action')
        ->editColumn('status', 'admin.datatable.status.status')
        ->rawColumns(['status', 'action'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.livevideo.list', $data);
  }

  public function create(Request $request)
  {
    $data['title'] = "Create Live Video";
    return view('admin.livevideo.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'day_no' => 'required|integer|min:1',
      'title'  => 'required',
    ]);

    LiveVideo::create([
      'day_no'        => $request->get('day_no'),
      'title'         => $request->get('title'),
      'description'   => $request->get('description'),
      'video_count'   => (int) $request->get('video_count', 0),
      'duration_mins' => (int) $request->get('duration_mins', 0),
      'video_url'     => $request->get('video_url'),
      'scheduled_at'  => $request->get('scheduled_at') ?: null,
      'status'        => $request->get('status'),
      'created_at'    => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/live-videos")->withSuccess("Great! Live video has been added");
  }

  public function edit(Request $request, $id)
  {
    $data['title'] = "Edit Live Video";
    $data['live_info'] = LiveVideo::where('id', $id)->first();

    return view('admin.livevideo.edit', $data);
  }

  public function update(Request $request)
  {
    $id = $request->get('id');
    $request->validate([
      'day_no' => 'required|integer|min:1',
      'title'  => 'required',
    ]);

    LiveVideo::where('id', $id)->update([
      'day_no'        => $request->get('day_no'),
      'title'         => $request->get('title'),
      'description'   => $request->get('description'),
      'video_count'   => (int) $request->get('video_count', 0),
      'duration_mins' => (int) $request->get('duration_mins', 0),
      'video_url'     => $request->get('video_url'),
      'scheduled_at'  => $request->get('scheduled_at') ?: null,
      'status'        => $request->get('status'),
      'updated_at'    => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/live-videos")->withSuccess("Great! Live video has been updated");
  }
}
