<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Excel extends Controller
{
    //

     public function downloadFile()
    {
      
        $myFile = public_path("upload/sampleexcelsheet.xlsx");
    	return response()->download($myFile);

        
       
    }	
}
