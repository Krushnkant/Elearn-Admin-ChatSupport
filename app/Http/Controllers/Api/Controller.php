<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function postImportExcel(Request $request)
    {
        if($request->file('excel') != null){
            DB::beginTransaction();
            try {
                $excel = $request->file('excel');
                $fileD = fopen($excel,"r");
                $column=fgetcsv($fileD);
                while(!feof($fileD)){
                 $rowData[]=fgetcsv($fileD);
                }
                $param = [];
                foreach ($rowData as $key => $value) {
                    if(!empty($value) && $value[0] != '') {
                        $category_name = $value[0];
                        $des_email = $value[1];
                        $type = $value[2];
                        $image = $value[3];
                        $image_thumb = $value[4];
                        $upload_file = $value[5];
                        $social_type = $value[6];
                        $title = $value[7];
                        $tags = $value[0].','.$value[8];

                        $cat_is_exists = Category::where(['cat_name' => $category_name])->first();
                        if($cat_is_exists) {
                            $cat_id = $cat_is_exists->id;
                        } else {
                            $cat_data = [
                                'cat_name'  => $category_name,
                                'status'    => "0",
                            ];
                            $cat_id = Category::insertGetId($cat_data);
                        }

                        $param[] = [
                            'cat_id'        => $cat_id,
                            'designer_id'   => $designer_id,
                            'type_image_id' => $type_image_id,
                            'image'         => $image,
                            'image_thumb'   => $image_thumb,
                            'upload_file'   => $upload_file,
                            'social_type'   => $social_type,
                            'title'         => $title,
                            'image_tags'    => $tags,
                            'status'        => "1",
                            'is_active'     => "1",
                        ];
                    }
                }
                $result = Products::insert($param);
                DB::commit();
                if ($result) {
                    return $this->sendResponse('Products has been uploaded successfully');
                } else {
                    return $this->sendError('Oops! Products not uploaded', 401);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Oops! Products not uploaded', 401);
            }
            
        }
    }
}
