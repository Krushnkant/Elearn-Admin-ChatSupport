<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;


class AjaxController extends Controller
{
    public function changeStatus(Request $request) {

		$table = $request->table;
		$id = $request->id;

		$query = 'UPDATE `' . $table . '` SET `status` = IF(`status` = "1", "0", "1") WHERE `id` = "' . $id . '"';
		$result = DB::update($query);
		if ($result) {
			die('1');
		} else {
			die('0');
		}
	}
 
	public function hardDelete(Request $request) {
		
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
}

