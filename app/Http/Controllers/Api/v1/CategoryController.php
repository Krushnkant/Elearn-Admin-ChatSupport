<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Category};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Response, DB, Mail, Auth;
use Illuminate\Support\Collection;

class CategoryController extends Controller
{
	public function index(Request $request)
	{
  	$query = Category::query();
  
  	$data = $query->where('status','1')
                    ->orderBy('created_at', 'ASC')
                    ->get()->toArray();
                    //->paginate($request->get('per_page') ? $request->get('per_page') : 10);
					//echo "<pre>"; print_r($data);die;
					
	$fdata = array();
	if(count($data) > 0) 
	{
		foreach($data as $d)
		{
			if($d['type']=="Knowledge")
			{
				$type = "Performance Domain";
			}else{
				$type =$d['type'];
			}
			$k['id'] = $d['id'];
			$k['name'] = $d['name'];
			$k['status'] = $d['status'];
			$k['type'] = $type;
			$k['created_at'] = $d['created_at'];
			$k['updated_at'] = $d['updated_at'];
			$fdata[] = $k;
		}
	} 
  	if(count($data) > 0) {
      return response()->json([
        'success' => true,
        'message' => "Category List.",
        'data'    => $fdata,
      ]);
    }
    return response()->json([
      'success' => false,
      'message' => "Data not found.",
    ]);
	}


  public function categorylist(Request $request)
	{
    $fields = ['id', 'name','status','type','created_at','updated_at'];

    $query = Category::query();
   

    $data = $query->where('status', 1)
  
                 ->select($fields)
               
             
                 ->get();
     // dd($data);
    //$unique = $data->unique('brand');
    //$unique->values()->all();
    //$genera = $data->groupBy('brand');
                    //$collection = collect($data);

    $collection = $data->unique('type')->values();
   
    function getTypeValue($value)
    {
        
        switch ($value) {
            case $value == "Domain":
                return 1;
                break;
            case $value == "Knowledge":
                return 2;
                break;
            case $value == "Approach":
                return 3;
                break;
            
            default:
                return 1;
                break;
        }
    }
    
    $result = (new Collection($collection->map(function($itemnew, $key) use ($request){
            //dd($item['brand']);
			
            return [
                'title' => ($itemnew->type=='Knowledge') ? 'Performance Domain' : $itemnew->type,
              
                
                'ListofData' => $itemnew->where('type', getTypeValue($itemnew->type))->get()
                    ->map(function($itemnew, $key){
                     
                        return [
                            "id" => $itemnew->id,
                            "name" => $itemnew->name,
                            "type" => ($itemnew->type=='Knowledge') ? 'Performance Domain' : $itemnew->type,
                           
                        ];
                }),
            ];
        })));

    //dd($result);
	//echo "<pre>"; print_r($result);die; 
    if(count($data) > 0) {
      return response()->json([
        "success" => true,
        "message" => "Category list",
        "data"    => $result,
        "count"   => count($data)
      ]);
    }
    return response()->json([
      "success" => false,
      "message" => "Oops! something went wrong"
    ]);
	}




}
