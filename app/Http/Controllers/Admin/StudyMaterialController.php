<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\StudyMaterial;
use DataTables;

class StudyMaterialController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Study Material List";

    if ($request->ajax())
    {
      $rows = StudyMaterial::orderBy('sort_order', 'asc');

      return Datatables::of($rows)
        ->editColumn('created_at', function($row){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($row->created_at));
        })
        ->editColumn('icon', function($row){
          return '<i class="bi ' . e($row->icon) . '"></i> <small>' . e($row->icon) . '</small>';
        })
        ->addColumn('action', 'admin.studymaterial.action')
        ->editColumn('status', 'admin.datatable.status.status')
        ->rawColumns(['status', 'action', 'icon'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.studymaterial.list', $data);
  }

  public function create(Request $request)
  {
    $data['title'] = "Create Study Material";
    return view('admin.studymaterial.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required',
    ]);

    StudyMaterial::create([
      'title'       => $request->get('title'),
      'description' => $request->get('description'),
      'icon'        => $request->get('icon') ?: 'bi-journal-bookmark-fill',
      'color'       => $request->get('color') ?: 'ic-blue',
      'sort_order'  => (int) $request->get('sort_order', 0),
      'status'      => $request->get('status'),
      'created_at'  => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/study-materials")->withSuccess("Great! Study material has been added");
  }

  public function edit(Request $request, $id)
  {
    $data['title']      = "Edit Study Material";
    $data['book_info']  = StudyMaterial::where('id', $id)->firstOrFail();
    return view('admin.studymaterial.edit', $data);
  }

  public function update(Request $request)
  {
    $id = $request->get('id');
    $request->validate([
      'title' => 'required',
    ]);

    StudyMaterial::where('id', $id)->update([
      'title'       => $request->get('title'),
      'description' => $request->get('description'),
      'icon'        => $request->get('icon') ?: 'bi-journal-bookmark-fill',
      'color'       => $request->get('color') ?: 'ic-blue',
      'sort_order'  => (int) $request->get('sort_order', 0),
      'status'      => $request->get('status'),
      'updated_at'  => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/study-materials")->withSuccess("Great! Study material has been updated");
  }
}
