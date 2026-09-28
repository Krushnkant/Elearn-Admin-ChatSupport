<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactEnquiry;
use Config;
use DataTables;

class EnquiryController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Enquiries";

    if ($request->ajax())
    {
      $rows = ContactEnquiry::orderBy('id', 'desc');

      return Datatables::of($rows)
        ->editColumn('created_at', function($row){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($row->created_at));
        })
        ->editColumn('subject', function($row){
          return $row->subject ?: 'General';
        })
        ->addColumn('excerpt', function($row){
          return e(\Illuminate\Support\Str::limit($row->message, 60));
        })
        ->addColumn('state', function($row){
          return $row->is_read
            ? '<span class="badge badge-secondary">Read</span>'
            : '<span class="badge badge-success">New</span>';
        })
        ->addColumn('action', function($row){
          return '<a href="'.url('admin/enquiries/'.$row->id).'" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>';
        })
        ->rawColumns(['state', 'action'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.enquiry.list', $data);
  }

  public function show(Request $request, $id)
  {
    $enquiry = ContactEnquiry::findOrFail($id);

    // Opening an enquiry is what "reading" it means here.
    if (!$enquiry->is_read) {
      $enquiry->update(['is_read' => 1]);
    }

    $data['title']   = "Enquiry from " . $enquiry->name;
    $data['enquiry'] = $enquiry;

    return view('admin.enquiry.show', $data);
  }
}
