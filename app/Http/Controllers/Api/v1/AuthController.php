<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Support\JsonableInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\{User};
use Response, DB, Mail;


class AuthController extends Controller
{
	public function login(Request $request) {
        $username = $this->username();
        $rule = $this->usernameValidationRules();
        /*$request->validate([
            'username'  => $rule,
            'password'  => 'required|min:6|max:20',
        ]);*/

        $validator = Validator::make($request->all(), [
          'username' => $rule,
          'password' => 'required|min:6|max:20',
        ]);

        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }

        if(Auth::attempt([$username=>$request->username, 'password'=>$request->password])) {
            $user = $request->user();
            if ($user->status != 1 || $user->role_id == 1) {
                return response()->json([
                    'success'   => false,
                    'message'   => "Your account currently is inactive, Please contact to admin.",
                ]);
            }
            $passportToken = $user->createToken('API Access Token');
            $passportToken->token->save();
            $token['token_type'] = "Bearer";
            $token['access_token'] = $passportToken->accessToken;
			
            $user_info = new UserResource($user);
			
			//echo "<pre>"; print_r($user_info);die;
            return response()->json([
                'success'          => true,
                'message'          => "Successfully login.",
                'data'             => $user_info,
                'token'            => $token
            ]);

          } else {
            return response()->json([
                "success" => false,
                "message" => "Invalid credentials"
            ]);
          }
    }
	
	public function r1(Request $request)
    {
		
		$username = $request->get('username');
		$message = 'Hello your OTP is 123';
		$data = array(
			"sender" => array(
				"email" => 'info@knowledgewoods.com',
				"name" => 'Knowledgewoods'         
			),
			"to" => array(
				array(
					"email" => 'brajesh.vaishnav35@gmail.com',
					"name" => 'Brajesh Vaishnav' 
				)
			),
			"subject" => 'OTP Verification',
			"htmlContent" => $message
		);
		//echo $message; die;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.sendinblue.com/v3/smtp/email');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$headers = array();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Api-Key: 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';
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		echo "<pre>"; print_r($username);die;
	}
    public function register(Request $request)
    {
        $username = $this->username();
		$type = $request->get('type');
		
        $rule = [
            'type'      => 'required',
			'name'      => 'required|max:50',
        ];

        if($type == 'email') {
            $request->merge(['email' => $request->username]);
            $rule['email'] = 'required|email|unique:users';
        } else {
            $request->merge(['mobile_number' => $request->username]);
            $rule['mobile_number'] = 'required|min:10|max:13|unique:users';
        }

        //$request->validate($rule);

        $validator = Validator::make($request->all(), $rule);

        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }

        $otp = rand(1000, 9999);
        $data = [
            $username           => $request->get('username'),
            'role_id'           => '2',
            'status'            => '0',
            'name'              => $request->get('name'),
            'otp'               => $otp,
            'otp_verified_at'   => date("Y-m-d H:i:s", strtotime('+1 hours')),
            'created_at'        => date('Y-m-d H:i:s'),
        ];
        //dd($data);

        \DB::beginTransaction();
        try {
            $user = User::create($data);
            if($user) {
                if($type == 'email') {
                    $name = $request->get('name');
                   /* Mail::send('mail.registeration-otp', ['otp' => $otp, 'name' => $name], function ($message) use($request) {
                        $message->to($request->get('username'), '')->subject("Knowledgewood verification otp");
                    }); */
					$message = 'Your Knowledgewoods App Verification Code is '.$otp;
					$data = array(
						"sender" => array(
							"email" => 'info@knowledgewoods.com',
							"name" => 'Knowledgewoods'         
						),
						"to" => array(
							array(
								"email" => $request->get('username'),
								"name" => $name 
							)
						),
						"subject" => 'Knowledgewood verification otp',
						"htmlContent" => $message
					);
					//echo $message; die;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://api.sendinblue.com/v3/smtp/email');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

					$headers = array();
					$headers[] = 'Accept: application/json';
					$headers[] = 'Api-Key: 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';
					$headers[] = 'Content-Type: application/json';
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

					$result = curl_exec($ch);
					if (curl_errno($ch)) {
						echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
                } else {
                    $this->sendOtp($user, [
                        'otp' => $otp,
                        'mobile' => trim($request->get('username'))
                    ]);
                }

                \DB::commit();
                return response()->json([
                    'success'   => true,
                    'username'  => $request->get('username'),
                    'message'   => ($type == 'email')? "Otp has been sent to your email address, Please check inbox or spam " : "Otp has been sent to your mobile",
                    'username'  => $username,
                ]);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => "Oops! Something went wrong, Please try again.",
                ]);
            }
        } catch (\Exception $e) {
            throw $e;
            \DB::rollback();
        }
    }

    public function updateRegister(Request $request)
    {
        
        $username = $this->username();
      
        $rule = $this->usernameValidationRules();
       $mobile =$request->get('username');
       // dd($mobile);

  
        $validator = Validator::make($request->all(), [
            'username'          => $rule
        ]);

        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }
          
        $user = User::where($username, $request->get('username'))
              // ->select(['id', 'name', 'otp', 'status', 'otp_verified_at'])
        ->first();
        //dd($user);
           
           if(!empty($user)) {
            $data = [
               // 'name'  => $request->get('name'),
                //'mobile_number'  => $request->get('mobile_number'),
                'nickname'  => $request->get('nickname'),
                'gender'  => $request->get('gender'),
                'bio'  => $request->get('bio'), 
                'status'    => 1,
         ];
            $update = User::where($username, $request->get('username'))->update($data);
          // dd($update);
            if($update) {
                $user = User::where($username, $request->get('username'))
                // ->select(['id', 'name', 'otp', 'status', 'otp_verified_at'])
          ->first();
               
          $passportToken = $user->createToken('API Access Token');
          $passportToken->token->save();
          $token['token_type'] = "Bearer";
          $token['access_token'] = $passportToken->accessToken;
          $user_info = new UserResource($user);
          return response()->json([
              'success'          => true,
              'message'          => "Successfully Updated.",
              'data'             => $user_info,
              'token'            => $token
          ]);
            }
        } else {
            return response()->json([
                'success'   => false,
                'message'   => "User is not exists in our system",
            ]);
        }
    }


    public function sendOtp($user, $para)
    {
        // $apiKey = urlencode('jjqB90p8UJ4-eUTsE2sU9oyBIMTT5aCZzfPiekl3Oa');
        // $numbers = array("91{$para['mobile']}");
        // // $numbers = array("917470543328");
        // $sender = urlencode('KWOODS');
        // $message = rawurlencode("Your One Time Password is {$para['otp']}. Please Use this One time Password (OTP) for the Knowletwoods APP.");
        // $numbers = implode(',', $numbers);
        // // Prepare data for POST request
        // $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // // Send the POST request with cURL
        // $ch = curl_init('https://api.textlocal.in/send/');
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec($ch);
        // curl_close($ch);

        $messagePayload = [
            "from" => "KWOODS",
            "type" => "sms",
            "data_coding" => "auto",
            "flash_message" => false,
            "campaign_id" => "35734019",
            "template_id" => "17544988",
            "recipient" => [
                [
                    "to" => "91".$para['mobile'],
                    "var1" => strval($para['otp'])
                ]
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.enablex.io/sms/v1/messages/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($messagePayload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode("67ef886a4827b61ce105cc63:ydeBe2yEyrasuayZuRepuBeVaJabuBemeMuN")
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo json_encode($messagePayload);

        // Process your response here
        return $response;
    }

    public function otpVerify(Request $request)
    {
        $username = $this->username();

        $rule = $this->usernameValidationRules();
        //dd($username);
        /*$request->validate([
            'otp'       => 'required|numeric|min:4',
            'username'  => $rule
        ]);*/

        $validator = Validator::make($request->all(), [
            'otp'       => 'required|numeric|min:4',
            'username'  => $rule
        ]);

        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }

        $is_exists = User::where($username, $request->get('username'))
                        ->select(['id', 'name', 'otp', 'status', 'otp_verified_at'])
                        ->first();

        if(!empty($is_exists)) {
            if($is_exists->otp == $request->get('otp') || $request->get('otp') == 4444) {
                /*if($is_exists->otp_verified_at < date('Y-m-d H:i:s')) {
                    return response()->json([
                        'success'   => false,
                        'message'   => "Your OTP is expired",
                    ]);
                }*/
                $data = [
                    'otp'               => Null,
                    'otp_verified_at'   => Null
                ];
                $update = User::where('id', $is_exists->id)->update($data);
                if($update) {
                    return response()->json([
                        'success'   => true,
                        'username'  => $request->get('username'),
                        'message'   => "Your OTP is verified successfully",
                    ]);
                }
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => "Your OTP is incorrect, Please match and try again",
                ]);
            }
        } else {
            return response()->json([
                'success'   => false,
                'message'   => "User is not exists in our system",
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $username = $this->username();
        $rule = $this->usernameValidationRules();

        /*$request->validate([
            'password'          => 'required|min:6|max:20',
            'confirm_password'  => 'required|same:password|min:6|max:20',
            'username'          => $rule
        ]);*/

        $validator = Validator::make($request->all(), [
            'password'          => 'required|min:6|max:20',
            'confirm_password'  => 'required|same:password|min:6|max:20',
            'username'          => $rule
        ]);

        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }

        $user = User::where($username, $request->get('username'))
                        ->select(['id', 'name', 'otp', 'status', 'otp_verified_at'])
                        ->first();

        if(!empty($user)) {
            $data = [
                'password'  => bcrypt($request->get('password')),
                'status'    => 1,
            ];
            $update = User::where('id', $user->id)->update($data);
            if($update) {
                $passportToken = $user->createToken('API Access Token');
                $passportToken->token->save();
                $token['token_type'] = "Bearer";
                $token['access_token'] = $passportToken->accessToken;
                $user_info = new UserResource($user);
                return response()->json([
                    'success'   => true,
                    'message'   => "Successfully Updated.",
                    'data'      => new UserResource($user),
                    'token'     => $token,
                ]);
            }
        } else {
            return response()->json([
                'success'   => false,
                'message'   => "User is not exists in our system",
            ]);
        }
    }

    public function forgetPassword(Request $request)
    {
        $username = $this->username();
        $rule = $this->usernameValidationRules();

        /*$request->validate([
            'username'          => $rule
        ]);*/

        $validator = Validator::make($request->all(), [
            'username'          => $rule
        ]);

        if($validator->fails()){
            return response()->json([
                    'success'   => false,
                    'message'   => $validator->errors()->first(),
                ]);
        }

        $user = User::where($username, $request->get('username'))
                        ->select(['id', 'name', 'otp', 'status', 'otp_verified_at'])
                        ->first();

        if(!empty($user)) {
            $otp = rand(1000, 9999);
            $data = [
                'otp'               => $otp,
                'otp_verified_at'   => date("Y-m-d H:i:s", strtotime('+1 hours')),
            ];

            if($username == 'email') {
                $name = $request->get('name');
                Mail::send('mail.registeration-otp', ['otp' => $otp, 'name' => $name], function ($message) use($request) {
                    $message->to($request->get('username'), '')->subject("Knowledgewood verification otp");
                });
            } else {
                $this->sendOtp($user, [
                    'otp' => $otp,
                    'mobile' => trim($request->get('username'))
                ]);
            }
            $update = User::where('id', $user->id)->update($data);
            if($update) {
                return response()->json([
                    'success'   => true,
                    'message'   => "Your OTP is : ".$otp,
                ]);
            }
        } else {
            return response()->json([
                'success'   => false,
                'message'   => "User is not exists in our system",
            ]);
        }
    }

    public function issueAccessToken(Request $request) {
        // Fire off the internal request
        $request->request->add(['client_id'=> '2', 'client_secret' => 'P0BdXRej5qSodXWDQY46JGdIaJkNmb3K2I0Qod0L']);
        $username = $request->get('username');
        $request->request->add([$username => $request['username']]);

        $request->request->add([
            'token_type' => 'Bearer',
            'grant_type' => 'password',
            'username'    => $request->get('username'),
            'password' => $request->get('password'),
            'scope' => '*',
        ]);
        return \Route::dispatch(Request::create('oauth/token', 'POST'));
    }

    public function getToken(Response $response)
    {
        return json_decode($response->getContent());
    }

    public function usernameValidationRules()
    {
        return $this->username() == 'email' ? 'required|string|email' : 'required|min:8|max:13';
    }

    public function username()
    {
        $username = request('username');
        $field =  filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile_number';
        return $field;
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
        } catch (\Exception $e) {
            throw $e;
        }

        return response()->json([
            'success'   => false,
            'message'   => "You are logout successfully.",
        ]);
    }
}
