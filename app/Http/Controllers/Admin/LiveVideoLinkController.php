<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\LiveVideo;
use App\Models\LiveVideoLink;
use DataTables;

class LiveVideoLinkController extends Controller
{
  public function index(Request $request, $liveVideoId)
  {
    $day = LiveVideo::findOrFail($liveVideoId);
    $data['title']     = "Videos — Day {$day->day_no}: {$day->title}";
    $data['day']       = $day;

    if ($request->ajax())
    {
      $rows = LiveVideoLink::where('live_video_id', $liveVideoId)
        ->orderBy('sort_order', 'asc');

      return Datatables::of($rows)
        ->editColumn('created_at', function($row){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($row->created_at));
        })
        ->editColumn('video_url', function($row){
          return $row->video_url
            ? '<a href="'.e($row->video_url).'" target="_blank">'.e(\Str::limit($row->video_url, 45)).'</a>'
            : '—';
        })
        ->addColumn('action', function($row) use ($liveVideoId) {
          return view('admin.livevideolink.action', ['id' => $row->id, 'liveVideoId' => $liveVideoId])->render();
        })
        ->editColumn('status', 'admin.datatable.status.status')
        ->rawColumns(['status', 'action', 'video_url'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.livevideolink.list', $data);
  }

  public function create(Request $request, $liveVideoId)
  {
    $data['title'] = "Add Video";
    $data['day']   = LiveVideo::findOrFail($liveVideoId);
    return view('admin.livevideolink.add', $data);
  }

  public function store(Request $request, $liveVideoId)
  {
    LiveVideo::findOrFail($liveVideoId);
    $request->validate([
      'title' => 'required',
    ]);

    LiveVideoLink::create([
      'live_video_id' => $liveVideoId,
      'title'         => $request->get('title'),
      'video_url'     => $request->get('video_url'),
      'duration_mins' => (int) $request->get('duration_mins', 0),
      'sort_order'    => (int) $request->get('sort_order', 0),
      'status'        => $request->get('status'),
      'created_at'    => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/live-videos/{$liveVideoId}/links")->withSuccess("Great! Video has been added");
  }

  public function edit(Request $request, $liveVideoId, $id)
  {
    $data['title']     = "Edit Video";
    $data['day']       = LiveVideo::findOrFail($liveVideoId);
    $data['link_info'] = LiveVideoLink::where('id', $id)->where('live_video_id', $liveVideoId)->firstOrFail();
    return view('admin.livevideolink.edit', $data);
  }

  public function update(Request $request, $liveVideoId)
  {
    $id = $request->get('id');
    $request->validate([
      'title' => 'required',
    ]);

    LiveVideoLink::where('id', $id)->where('live_video_id', $liveVideoId)->update([
      'title'         => $request->get('title'),
      'video_url'     => $request->get('video_url'),
      'duration_mins' => (int) $request->get('duration_mins', 0),
      'sort_order'    => (int) $request->get('sort_order', 0),
      'status'        => $request->get('status'),
      'updated_at'    => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/live-videos/{$liveVideoId}/links")->withSuccess("Great! Video has been updated");
  }
}
