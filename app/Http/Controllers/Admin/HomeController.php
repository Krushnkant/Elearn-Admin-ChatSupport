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

		// Real platform stats (raw table names — Course maps to `cources`,
		// and some model classes are mismatched, so query tables directly).
		$data['stats'] = [
			'students'     => DB::table('users')->where('role_id', 2)->count(),
			'courses'      => DB::table('cources')->count(),
			'ebooks'       => DB::table('ebooks')->count(),
			'questions'    => DB::table('questions')->count(),
			'categories'   => DB::table('categories')->count(),
			'assessments'  => DB::table('assessments')->count(),
			'transactions' => DB::table('transactions')->count(),
			'revenue'      => (float) DB::table('transactions')->where('status', 1)->sum('amount'),
		];

		// Latest 5 registered students for a quick "recent activity" panel.
		$data['recentUsers'] = DB::table('users')
			->where('role_id', 2)
			->orderByDesc('id')
			->limit(5)
			->get(['id', 'name', 'email', 'mobile_number', 'created_at']);

		return view('admin.dashboard.index', $data);
	}
}

