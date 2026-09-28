<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\HubCard;
use DataTables;

class HubCardController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Learning Hub Cards";

    if ($request->ajax())
    {
      $rows = HubCard::orderBy('sort_order', 'asc');

      return Datatables::of($rows)
        ->editColumn('created_at', function($row){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($row->created_at));
        })
        ->editColumn('icon', function($row){
          return '<i class="bi ' . e($row->icon) . '"></i> <small>' . e($row->icon) . '</small>';
        })
        ->editColumn('link_url', function($row){
          return $row->link_url ? e($row->link_url) : '<em>Coming soon</em>';
        })
        ->addColumn('action', 'admin.hubcards.action')
        ->editColumn('status', 'admin.datatable.status.status')
        ->rawColumns(['status', 'action', 'icon', 'link_url'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.hubcards.list', $data);
  }

  public function create(Request $request)
  {
    $data['title'] = "Add Learning Hub Card";
    return view('admin.hubcards.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required',
    ]);

    HubCard::create([
      'title'       => $request->get('title'),
      'description' => $request->get('description'),
      'icon'        => $request->get('icon') ?: 'bi-journal-bookmark-fill',
      'color'       => $request->get('color') ?: 'ic-blue',
      'link_url'    => $request->get('link_url') ?: null,
      'is_new'      => $request->boolean('is_new') ? 1 : 0,
      'sort_order'  => (int) $request->get('sort_order', 0),
      'status'      => $request->get('status'),
      'created_at'  => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/hub-cards")->withSuccess("Great! Learning hub card has been added");
  }

  public function edit(Request $request, $id)
  {
    $data['title']     = "Edit Learning Hub Card";
    $data['card_info'] = HubCard::where('id', $id)->firstOrFail();
    return view('admin.hubcards.edit', $data);
  }

  public function update(Request $request)
  {
    $id = $request->get('id');
    $request->validate([
      'title' => 'required',
    ]);

    HubCard::where('id', $id)->update([
      'title'       => $request->get('title'),
      'description' => $request->get('description'),
      'icon'        => $request->get('icon') ?: 'bi-journal-bookmark-fill',
      'color'       => $request->get('color') ?: 'ic-blue',
      'link_url'    => $request->get('link_url') ?: null,
      'is_new'      => $request->boolean('is_new') ? 1 : 0,
      'sort_order'  => (int) $request->get('sort_order', 0),
      'status'      => $request->get('status'),
      'updated_at'  => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/hub-cards")->withSuccess("Great! Learning hub card has been updated");
  }
}
