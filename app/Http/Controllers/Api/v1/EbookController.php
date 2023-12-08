<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User, Category, Course, Question, Question_option, Answer, TestResult, Ebook, Assessment};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Response, DB, Mail;

class EbookController extends Controller
{
    //
    public function ebookList(Request $request)
	{
	  	$query = Course::query();
	  
	  
	  	$data['ebooks'] = Ebook::where('status', 1)
	  		->orderBy('created_at', 'desc')
	  		->limit(20)
	  		->get(['id', 'title', 'image', 'ebook', 'created_at']);

	  	if(count($data) > 0) {
	      return response()->json([
	        'success' => true,
	        'message' => "Data successfully found.",
	        'data'    => $data,
	      ]);
	    }
	    return response()->json([
	      'success' => false,
	      'message' => "Data not found.",
	    ]);
	}


    public function ebookListByname(Request $request)
	{
		   $data = Ebook::where('status', 1)->get([
		  	'id', 'title', 'ebook', 'description', 'price', 'image', 'created_at'
		  ]);
		  foreach ($data as $item){
			$item->description=strip_tags($item->description);
			}
		 $recommendation = Ebook::where('status', 1)
		  	->where('recommendation', 1)
		  	->get([
		   		'id', 'title', 'ebook', 'description', 'price', 'image', 'created_at'
		  	]);
			  foreach ($recommendation as $item){
				$item->description=strip_tags($item->description);
				}
		   $top_paid = Ebook::where('status', 1)
		  	->where('top_paid', 1)
		   	->get([
		  		'id', 'title', 'ebook', 'description', 'price', 'image', 'created_at'
		  	]);
			  
			  foreach ($top_paid as $item){
				$item->description=strip_tags($item->description);
				}
			  
			$array = ([
					'data'  => $data,
				   'top_paid' => $top_paid,
				   'recommendation' => $recommendation]);

		if(count($array) > 0) {
			return response()->json([
	            'success'	=> true,
	            'message'   => "Ebook list.",
	            'response' => $array
	          
	        ]);
	  	}
		 
	  	return response()->json([
	        'success'	=> false,
	        'message'   => "Data not found.",
	    ]);
	}
}
