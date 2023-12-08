<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Validator, Session, Redirect, Response, DB, Config, File, Mail;


class AuthController extends Controller
{
    public function login(Request $request)
    {
		$data['title'] = "Login";
		return view('auth.login', $data);
	}

	public function getUpdatePassword(Request $request)
	{
		$data['title'] = "Change password";
		return view('auth.update-password', $data);
	}

	public function postUpdatePassword(Request $request)
	{
		$data = $request->validate([
            'old_password' 		=> 'required',            
            'password' 			=> 'required',  
            'confirm_password' 	=> 'required|same:password',  
          ]);
        if (\Hash::check($request->old_password, $request->user()->password)) {

            $updateArr['password'] = bcrypt($request->password);
            $where = ['id' => $request->user()->id, 'role_id' => 1];
            $user = $request->user();
           // $update = User::where($where)->update($updateArr);
            $update = User::where('id',$request->user()->id)->first();
            $update->password = bcrypt($request->password);
            $update->save();
            //dd($update);
           	$login_success = Auth::loginUsingId(1);
           	if( $login_success ){
             	return Redirect::to("admin/change-password")->withSuccess("New Password create successfully");
           	}
        } else {
            return Redirect::to("admin/change-password")->withWarning("Old password does not match");
        }
        return Response::error('Something goes to wrong. Please try again');
	}
}

