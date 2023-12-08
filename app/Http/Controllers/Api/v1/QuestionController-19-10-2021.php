<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionCollection;
use App\Models\{User, Category, Question, Question_option, Ebook, Transaction};
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Response, DB, Mail, Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Dompdf\Dompdf;


class QuestionController extends Controller
{
	public function index_oldd(Request $request, $assessment_id)
	{
		DB::enableQueryLog();
		$is_plan_active = $request->user()->activeMembership();
		//dd($is_plan_active);
		$query = Question::query();

		if(!empty($request->get('category_id'))) {
			$query->where('category_id',$request->get('category_id'));
			
		}

		if(!empty($request->get('domain_id'))) {
			$domain_id = $request->get('domain_id');
			$query->whereIn('category_id',$request->get('domain_id'));
		}
		
		if(!empty($request->get('knowledge_id'))) {
			//$knowledge_id = explode(',', $request->get('knowledge_id'));
			$knowledge_id = $request->get('knowledge_id');
			//dd($knowledge_id);
			$query->whereHas('categoryQuestion', function($q) use ($knowledge_id) {
				$q->whereIn('category_id', $knowledge_id);
			});
		}

		if(!empty($request->get('approach_id'))) {
			$approach_id = $request->get('approach_id');
			$query->whereHas('categoryQuestion', function($q) use ($approach_id) {
				$q->whereIn('category_id', $approach_id);
			});
		}
		$today_date = date('Y-m-d');
		/*$is_plan_active = Transaction::where('user_id', $user_id)
										->where('start_date', '<=', $today_date)
        								->where('end_Date', '>=', $today_date)
										->orderBy('created_at', 'desc')
										->first();*/
		$limit = $request->get('limit');
		if(!empty($is_plan_active)) {
			//dd('123');
			$query->limit(25);
		} else if(empty($is_plan_active)) {
			//dd($limit);
			$query->limit(10);
		}

		if(!empty($request->get('set'))) {
			$is_plan_active = $request->user()->activeMembership();
			$set = $request->get('set');
			if($is_plan_active->plan == "Silver") {
				$query->where('silver_set', $set);
			}
			if($is_plan_active->plan == "Gold") {
				$query->where('gold_set', $set);
			}
		}
	  	$data = $query->where('assessment_id', $assessment_id)->with([
	  						'course',
	  						'questionOptions' => function($q) {
	  							$q->select(['id', 'question_id', 'options', 'image', 'is_correct']);
	  						}
	  					])
			  			->select('id', 'title', 'question_type', 'explanation', 'assessment_id')
			  			->orderByRaw("RAND()")
						//->get();
						->paginate($request->get('per_page') ? $request->get('per_page') : 10);
						//->paginate($request->get('per_page') ? $request->get('per_page') : 10);
			  			//dd(DB::getQueryLog());
	  	if(count($data) > 0) {
			return response()->json([
	            'success'	=> true,
	            'message'   => "Question List.",
	            'data'      => $data,
	            'count'     => count($data),
	        ]);
	  	}
	  	return response()->json([
	        'success'	=> false,
	        'message'   => "Data not found.",
	    ]);
	}

	public function index(Request $request, $assessment_id,$set)
	{
		//DB::enableQueryLog();

		$is_plan_active = $request->user()->activeMembership();
		//dd($is_plan_active);
		$query = Question::query();
	 
	
		 $limit = $request->get('limit');
		 if(empty($is_plan_active)) {
			//dd($limit);
			$query->limit(75);
		} else if($is_plan_active->plan == "Silver") {
			// dd('123');
			 $query->limit(75);
		 } else if($is_plan_active->plan == "Gold") {
			// dd('123');
			 $query->limit(75);
		 } 
		
		if(!empty($request->get('set'))) {

			$is_plan_active = $request->user()->activeMembership();
			
			$set = $request->get('set');
			//dd($set);


			if(!isset($is_plan_active->plan)) {
				return response()->json([
			        'success'	=> false,
			        'message'   => "User must have the active membership",
			    ]);
			}
			else if($is_plan_active->plan == "Silver") {
				
				$query->where('silver_set', $set);
			}
			else if($is_plan_active->plan == "Gold") {
			
				$query->where('gold_set', $set);
			}
		}
 
	

		//to view category data search
		if($request->get('category_id')) {
			$sub_category_ids = explode(',',$request->get('category_id'));
			//$query->where('category_id', $request->get('category_id'));
			$query->whereIN('sub_category_id',$sub_category_ids);
		  }
		// print_r($request);die;
		/*if($set) {
			$fset = 'set'.$set;
			$query->where('set_type', $fset);
		  } */
	     
		//echo $set;die;
	  	$data = $query->where('assessment_id', $assessment_id)
		      ->with([
				'course',
				'questionOptions' => function($q) {
					$q->select(['id', 'question_id', 'options', 'image', 'is_correct']);
				}
			])
			->select('id', 'title', 'question_type', 'explanation', 'assessment_id')
			->orderByRaw("RAND()")
			->get();
			//->paginate($request->get('per_page') ? $request->get('per_page') : 25);

	  	if(count($data) > 0) {
			return response()->json([
	            'success'	=> true,
	            'message'   => "Question List.",
	            'data'      => $data,
	            'count'     => count($data),
	        ]);
	  	}
	  	return response()->json([
	        'success'	=> false,
	        'message'   => "Data not found.",
	    ]);
		
	}
	public function startTest(Request $request, $assessment_id,$set)
	{
		
		$is_plan_active = $request->user()->activeMembership();
		//dd($is_plan_active); die;
		$query = Question::query();
	 
	   if(!empty($request->get('set'))) {
          $is_plan_active = $request->user()->activeMembership();
		   $set = $request->get('set');
		   //dd($set);
		   if(!isset($is_plan_active->plan)) {
			   return response()->json([
				   'success'	=> false,
				   'message'   => "User must have the active membership",
			   ]);
		   }
		   else if($is_plan_active->plan == "Silver") {
			   
			   $query->where('silver_set', $set);
		   }
		   else if($is_plan_active->plan == "Gold") {
		   
			   $query->where('gold_set', $set);
		   }
	   }

	  
	   $total_set = '';
  
	   if(!isset($is_plan_active)) {
		
			$limit = 180;
			$GetQuestions = Question::whereIn('category_id',[1])->pluck('id')->take(180)->toArray();
	   
			$allcategory = array_merge($GetQuestions);
		 
			$total_questions = Question::whereIN('id', $allcategory)->count();
			$total_set = intval(floor($total_questions / 180));
			

  		 } else if($is_plan_active->plan == "Silver") { 

	  	
	 		$limit = 180;

			 $limit = 180;
			 $GetQuestions = Question::whereIn('category_id',[1])->pluck('id')->take(900)->toArray();
		
			 $allcategory = array_merge($GetQuestions);
		  
			 $total_questions = Question::whereIN('id', $allcategory)->count();
			 $total_set = intval(floor($total_questions / 180));
		
  
		} else if($is_plan_active->plan == "Gold") {
	  
	  		 $limit = 180;

			   $limit = 180;
			   $GetQuestions = Question::whereIn('category_id',[1])->pluck('id')->toArray();
		  
			   $allcategory = array_merge($GetQuestions);
			
			   $total_questions = Question::whereIN('id', $allcategory)->count();
			   $total_set = intval(floor($total_questions / 180));
			
		} 
      

	  //$category = 1,2,3;
	   $data = $query->whereIN('id', $allcategory)->where('assessment_id', $assessment_id)
		  ->with([
				'course',
				'questionOptions' => function($q) {
					$q->select(['id', 'question_id', 'options', 'image', 'is_correct']);
				}
			])
			->select('id', 'title', 'question_type', 'explanation', 'assessment_id')
			->paginate($limit, ['*'],'page', $set);
			//->get();
			//->count();
			//dd($data);		


		

	  	if(count($data) > 0) {
			return response()->json([
	            'success'	=> true,
	            'message'   => "Question List.",
	            'data'      => $data,
	            'count'     => count($data),
				'total_set' => $total_set
	        ]);
	  	}
	  	return response()->json([
	        'success'	=> false,
	        'message'   => "Data not found.",
	    ]);
		
	}

	public function questionsSetListNew(Request $request, $assessment_id, $set)
	{
		//dd('Enter');
	$set_page = $set;
		
	$is_plan_active = $request->user()->activeMembership();
	$query = Question::query();
	

	$Domain = '';
	$Knowledge =''; 
	$Approach = '';

	$domain=$request->get('Domain');
	$knowledege=$request->get('Knowledge');
	$approach=$request->get('Approach');

	
	//dd($knowledege);
	//$serialized = json_decode($knowledege);
	$domain_exp = explode(",", $domain);
	//dd($domain_exp);
	$domain1=$domain_exp[0]  ?? Null ;

	$domain2=$domain_exp[1]  ?? Null ;
	$domain3=$domain_exp[2]  ??  Null ;
	//dd($domain3);
	//$domain3=$domain_exp[2]  ??  Null ;
		
	$knowledege_exp = explode(",", $knowledege);

	$approach_exp = explode(",",$approach);
	$approach1=$approach_exp[0]  ?? Null ;

	$approach2=$approach_exp[1]  ?? Null ;

	$no_domain1='0'; 
	if($domain1==1)
	{
		$no_domain1 = '750';
	}
	else if($domain1==2)
	{
		$no_domain1 = '900';
	}
	else if($domain1==3)
	{
		$no_domain1 = '150';
	}
	
	$no_domain2='0';
	if($domain2==1)
	{
		$no_domain2 = '750';
	}
	else if($domain2==2)
	{
		$no_domain2 = '900';
	}
	else if($domain2==3)
	{
		$no_domain2 = '150';
	}
	
	$no_domain3='0';
	if($domain3==1)
	{
		$no_domain3 = '750';
	}
	else if($domain3==2)
	{
		$no_domain3 = '900';
	}
	else if($domain3==3)
	{
		$no_domain3 = '150';
	}
	
	$no_approach1='0'; 
	if($approach1==11)
	{
		$no_approach1 = '900';
	}
	else if($approach1==12)
	{
		$no_approach1 = '900';
	}
	
	$no_approach2='0'; 
	if($approach2==11)
	{
		$no_approach2 = '900';
	}
	else if($approach2==12)
	{
		$no_approach2 = '900';
	}
	
	$Getpeople = Question::where('sub_category_id',$domain1)->take($no_domain1)->pluck('id')->toArray();
//	dd($Getpeople);
		
	//$process = Question::where('sub_category_id',$domain2)->take(900)->get();
	//dd($process);	
	//$que2 = intval(floor($process / 90));
	
	//dd($que2);
	$Getprocess = Question::where('sub_category_id',$domain2)->take($no_domain2)->pluck('id')->toArray();
	
	//$business = Question::where('sub_category_id', $domain3)->take(150)->get();
	
	//$que3 = intval(floor($business / 15));
	$Getbusiness = Question::where('sub_category_id',$domain3)->take($no_domain3)->pluck('id')->toArray();
	
	

	$kn1 = Question::whereIn('sub_category_id', $knowledege_exp)->count();
    $Getknowledge = Question::whereIN('sub_category_id',$knowledege_exp)->take($kn1)->pluck('id')->toArray();
	
    //$approchquelist = Question::where('sub_category_id', $approach1)->take(900)->get();
	//dd($approchquelist);
	//$approachpercentage = intval(floor($approchquelist / 90));
	$GetapproachAgile = Question::where('sub_category_id',$approach1)->take($no_approach1)->pluck('id')->toArray();
		
	//$approchquelistHybrid = Question::where('sub_category_id', $approach2)->take(900)->get();
	//$approachpercentageHybrid = intval(floor($approchquelistHybrid / 90));
	$GetapproachHybrid = Question::where('sub_category_id',$approach2)->take($no_approach2)->pluck('id')->toArray();
	//dd( $GetapproachHybrid); 


	 $allcategory = array_merge($Getpeople,$Getprocess,$Getbusiness,$Getknowledge,$GetapproachAgile,$GetapproachHybrid);
	 
	 $allcategory2 = array_merge($Getpeople,$Getprocess,$Getbusiness);
   
	 $Countquestion = count($allcategory);
   
   //echo "<pre>"; print_r($Countquestion); die;

	//$total_questions = Question::whereIn('sub_category_id', $allcategory)->count();
	$total_questions = $Countquestion;
	
	//echo "<pre>"; print_r($total_questions); die;
	
	 if(empty($is_plan_active)) {
		return response()->json([
			'success'	=> false,
			'message'   => "User must have the active membership",
		]);
	} else if($is_plan_active->plan == "Silver") {
		// dd('123');
		 $query->limit(5);
	 } else if($is_plan_active->plan == "Gold") {
		// dd('123');
		 $query->limit(10);
	 } 
	
	
		if(!isset($is_plan_active)) {
			return response()->json([
				'success'	=> false,
				'message'   => "User must have the active membership",
			]);
		}
		else if($is_plan_active->plan == "Silver") {
			
			$query->where('silver_set', $set);
		}
		else if($is_plan_active->plan == "Gold") {
		
			$query->where('gold_set', $set);
		}
	

	
	 $total_set = '';
	if($total_questions == 750)
	{		
		$total_set = intval(floor($total_questions / 75));		
        $limit = 75;
	} 
	else if($total_questions == 900)
	{
		$total_set = intval(floor($total_questions / 90));
		$limit = 90;
		//$query->simplePaginate($limit, ['*'],'page', $total_set);		 
	}
	else if($total_questions== 150 )
	{
		$total_set = intval(floor($total_questions / 15));
		$limit = 15;
	} 
	else if($total_questions==1800 )
	{
		$total_set = intval(floor($total_questions / 180));
		$limit = 180;
		//$query->simplePaginate($limit, ['*'],'page', $set);
	}
	else if($total_questions==1650 )
	{
		$total_set = intval(floor($total_questions / 165));
		$limit = 165;
	}
	else if($total_questions==1650 )
	{
		$total_set = intval(floor($total_questions / 165));
		$limit = 165;
	}
	else if($total_questions==1050 )
	{
		$total_set = intval(floor($total_questions / 105));
		$limit = 105;
	}
	else if($total_questions)
	{
		$total_set = intval(floor($total_questions / 180));
		$limit = 180;
		//$query->simplePaginate($limit, ['*'],'page', $set);
	}
			
		$data  = Question::whereIn('id', $allcategory)->where('assessment_id', $assessment_id)->with([
				'course',
				'questionOptions' => function($q) {
					$q->select(['id', 'question_id', 'options', 'image', 'is_correct']);
				}
			])
			->select('id','sub_category_id', 'title','question_type','explanation','assessment_id')
			->paginate($limit, ['*'], 'page', $set);
			//->get();

		//echo "<pre>"; print_r($allcategory); die;	 
			
			
	  	if(count($data) > 0) {
			return response()->json([
	            'success'	=> true,
	            'message'   => "Question Set List.",
	            'question_set'      => $data,
	           	'count'     => count($data),
				'total_questions' => $total_questions,
				'total_set' => $total_set
				
	        ]);
	  	}
	  	return response()->json([
	        'success'	=> false,
	        'message'   => "Data not found.",
	    ]);
		
	}
	
	public function ebookList(Request $request)
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
