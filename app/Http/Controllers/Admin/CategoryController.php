<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\Category;
use DataTables;
use Illuminate\Routing\Controller as BaseController;


class CategoryController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Category List";

    if ($request->ajax())
    {
        $data = Category::orderBy('created_at', 'desc');
                  return Datatables::of($data)
                  ->editColumn('created_at', function($data){
                    return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
                  })
                  ->addColumn('action', 'admin.datatable.action.category.category-action')
                  ->editColumn('status', 'admin.datatable.status.category-status')
                  ->rawColumns(['status', 'action'])
                  ->addIndexColumn()
                  ->make(true);
    }

    return view('admin.category.list', $data);
  }

  public function create(Request $request)
	{
    $data['title'] = "Create Category";
    return view('admin.category.add', $data);  
  }
  public function store(Request $request)
  {
    $request->validate([
      'name'    => 'required|unique:categories',
      'type'    => 'required',
    ]);
  
    $data = new Category;
    $data->name         = $request['name'];
    $data->status       =$request['status'];
    $data->type         =$request['type'];
    $data->created_at   =date('Y-m-d H:i:s');
    if($data->save()) {
      return Redirect::to("admin/category")->withSuccess("Great! Event has been added");
    }
    return Redirect::to("admin/category")->withWarning("Oops! something went wrong");
  }
  public function edit(Request $request, $id)
  {
    $data['title'] = "Edit Category";
    $data['category'] = Category::where('id',$id)->first();
    
    return view('admin.category.edit',$data);  
  }

  public function update(Request $request)
  {
    $data = Category::find($request->id);
    $request->validate([
      'name'    => 'required|unique:categories,name,'.$request->id,
      'type'    => 'required',
    ]);

    $data = Category::find($request->id);
    $data->name       = $request->name;
    $data->status     = $request->status == 1 ? 1 : 0;
    $data->type       = $request->type;
    $data->updated_at = date('Y-m-d H:i:s');

    if($data->save()) {
      return Redirect::to("admin/category")->withSuccess("Great! Info has been updated");
    }
    return Redirect::to("admin/category")->withWarning("Oops! something went wrong");
  }

  public function hardDelete(Request $request){

    $table = $request->get('table');
    $id = $request->get('id');
    $where = array('id' => $id);
    $result = DB::table($table)->where($where)->delete();

    if ($result) {
      die('1');
    } else {
      die('0');
    }
  }

  public function createSlug($name, $id = Null )
  {
    $seo_name = Category::slug($name);

    $is_exists = $this->getRelatedSlugs($seo_name, $id);

    if($is_exists == 0) {
      return $seo_name;
    }

    for ($i = 1; $i <= 10; $i++) {
      $newSlug = $seo_name.'-'.$i;
      $unique = $this->getRelatedSlugs($newSlug, $id);
      if($unique == 0) {
        return $newSlug;
      }
    }
    throw new \Exception('Can not create a unique slug');
  }

  protected function getRelatedSlugs($seo_name, $id = Null)
  {
    $query = Category::query();
    if($id){
      $query->where('id','!=',$id);     
    }
    return $query->select('name')
                ->where('name', $seo_name)
                ->count();
  }

  public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('login');
}
  

}
