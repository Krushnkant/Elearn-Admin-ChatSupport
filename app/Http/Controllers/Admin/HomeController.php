<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;


class HomeController extends Controller
{
    public function index(Request $request)
    {
		$data['title'] = "Dashboard";
		return view('admin.dashboard.index', $data);
	}
}

