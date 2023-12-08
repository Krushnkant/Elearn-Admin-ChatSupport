<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\Ebook;
use DataTables;
use Illuminate\Routing\Controller as BaseController;


class EBookController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Ebook List";

    if ($request->ajax())
    {
      $data = Ebook::orderBy('created_at', 'desc');

      return Datatables::of($data)
        ->editColumn('created_at', function($data){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
        })
        ->addColumn('action', 'admin.datatable.action.ebooks.ebooks-action')
        ->editColumn('status', 'admin.datatable.status.status')
        ->rawColumns(['status', 'action'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.ebook.list', $data);
  }

  public function create(Request $request)
	{
    $data['title'] = "Create Ebook";
    return view('admin.ebook.add', $data);  
  }
  public function store(Request $request)
  {
    $request->validate([
      'title'    => 'required|unique:ebooks',
      'image'    => 'required',
      'price' => 'required'
    ]);
  
    $data = [
      'title'         => $request->get('title'),
      'description'   => $request->get('description'),
      'status'        => $request->get('status'),
      'price'         => $request->get('price'),
      'recommendation' => $request->get('recommendation') == 1 ? 1 : 0,
      'top_paid' => $request->get('top_paid') == 1 ? 1 : 0,
      'created_at'    => date('Y-m-d H:i:s'),
    ];

    if($request->hasFile('ebook')) {
      $files = $request->file('ebook');
      $destinationPath = 'public/ebooks/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $fileebook = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $fileebook);
      $data['ebook'] = $fileebook;
    }
	
	if($request->hasFile('image')) {
      $files = $request->file('image');
      $destinationPath = 'public/ebooks/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image'] = $profileImage;
    }
    $insert = Ebook::insert($data);
    if($insert) {
      return Redirect::to("admin/ebooks")->withSuccess("Great! Event has been added");
    }
    return Redirect::to("admin/ebooks")->withWarning("Oops! something went wrong");
  }
  public function edit(Request $request, $id)
  {
    $data['title'] = "Edit Ebook";
    $data['ebook_info'] = Ebook::where('id',$id)->first();
    
    return view('admin.ebook.edit',$data);  
  }

  public function update(Request $request)
  {
    $id = $request->get('id');
    $request->validate([
      'title'    => 'required|unique:ebooks,image,'.$id,
      'image'    => 'nullable',
      'price' => 'required'
    ]);
  
    $data = [
      'title' => $request->get('title'),
      'description' => $request->get('description'),
      'status' => $request->get('status'),
      'price' => $request->get('price'),
      'recommendation' => $request->get('recommendation') == 1 ? 1 : 0,
      'top_paid' => $request->get('top_paid') == 1 ? 1 : 0,
      'updated_at' => date('Y-m-d H:i:s')
    ];

    if($request->hasFile('image')) {
      $image = Ebook::where('id', $id)->select(['image'])->first();
      $d_file = 'public/ebooks/' . $image->originalImage;
      File::delete($d_file);
      $files = $request->file('image');
      $destinationPath = 'public/ebooks/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $profileImage = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $profileImage);
      $data['image'] = $profileImage;
    }

    if($request->hasFile('ebook')) {
      $ebook = Ebook::where('id', $id)->select(['ebook'])->first();
      $d_file = 'public/ebooks/' . $ebook->originalEbook;
      File::delete($d_file);
      $files = $request->file('ebook');
      $destinationPath = 'public/ebooks/'; // upload path
      $rand = md5(time() . mt_rand(100000000, 999999999));
      $ebookfile = $rand . "." . @$files->getClientOriginalExtension();
      $files->move($destinationPath, $ebookfile);
      $data['ebook'] = $ebookfile; 
    } 

    $update = Ebook::where('id', $id)->update($data);

    if($update) {
      return Redirect::to("admin/ebooks")->withSuccess("Great! Info has been updated");
    }
    return Redirect::to("admin/ebooks")->withWarning("Oops! something went wrong");
  }
}
