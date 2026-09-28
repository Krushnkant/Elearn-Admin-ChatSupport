<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;
use App\Models\ContentSetting;

class ContentManagementController extends Controller
{
  public function index(Request $request)
  {
    $data['title']     = "Content Management";
    $data['mock_test'] = ContentSetting::group('mock_test');
    return view('admin.content-management.index', $data);
  }

  public function updateMockTest(Request $request)
  {
    $request->validate([
      'title'       => 'required|max:100',
      'badge_1'     => 'required|max:40',
      'badge_2'     => 'required|max:40',
      'badge_3'     => 'required|max:40',
      'badge_4'     => 'required|max:40',
      'description' => 'required|max:500',
    ]);

    foreach (['title', 'badge_1', 'badge_2', 'badge_3', 'badge_4', 'description'] as $field) {
      ContentSetting::setValue('mock_test.' . $field, trim($request->get($field)));
    }

    return Redirect::to("admin/content-management")->withSuccess("Great! Mock test page content has been updated");
  }
}
