<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File, Mail, Auth;
use App\Models\SubscriptionPlan;
use DataTables;

class SubscriptionPlanController extends Controller
{
  public function index(Request $request)
  {
    $data['title'] = "Subscription Plans";

    if ($request->ajax())
    {
      $rows = SubscriptionPlan::orderBy('sort_order', 'asc');

      return Datatables::of($rows)
        ->editColumn('created_at', function($row){
          return date(Config::get('constants.DATE_FORMAT'), strtotime($row->created_at));
        })
        ->editColumn('icon', function($row){
          return '<i class="bi ' . e($row->icon) . '"></i> <small>' . e($row->icon) . '</small>';
        })
        ->editColumn('annual_price', function($row){
          return '₹' . number_format($row->annual_price);
        })
        ->addColumn('action', 'admin.subscriptionplans.action')
        ->editColumn('status', 'admin.datatable.status.status')
        ->rawColumns(['status', 'action', 'icon'])
        ->addIndexColumn()
        ->make(true);
    }

    return view('admin.subscriptionplans.list', $data);
  }

  public function create(Request $request)
  {
    $data['title'] = "Add Subscription Plan";
    return view('admin.subscriptionplans.add', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required',
    ]);

    SubscriptionPlan::create([
      'theme'            => $request->get('theme') ?: 'basic',
      'name'             => $request->get('name'),
      'icon'             => $request->get('icon') ?: 'bi-mortarboard-fill',
      'annual_price'     => (int) $request->get('annual_price', 0),
      'annual_old_price' => (int) $request->get('annual_old_price', 0),
      'monthly_price'    => (int) $request->get('monthly_price', 0),
      'ideal_text'       => $request->get('ideal_text'),
      'cta_text'         => $request->get('cta_text') ?: 'Choose Plan',
      'inherit_text'     => $request->get('inherit_text'),
      'features'         => $this->cleanFeatures($request->get('features')),
      'badge_type'       => $request->get('badge_type') ?: null,
      'badge_text'       => $request->get('badge_text') ?: null,
      'sort_order'       => (int) $request->get('sort_order', 0),
      'status'           => $request->get('status'),
      'created_at'       => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/subscription-plans")->withSuccess("Great! Subscription plan has been added");
  }

  public function edit(Request $request, $id)
  {
    $data['title']        = "Edit Subscription Plan";
    $data['plan_info']    = SubscriptionPlan::where('id', $id)->firstOrFail();
    $data['feature_rows'] = $data['plan_info']->feature_list;
    return view('admin.subscriptionplans.edit', $data);
  }

  public function update(Request $request)
  {
    $id = $request->get('id');
    $request->validate([
      'name' => 'required',
    ]);

    SubscriptionPlan::where('id', $id)->update([
      'theme'            => $request->get('theme') ?: 'basic',
      'name'             => $request->get('name'),
      'icon'             => $request->get('icon') ?: 'bi-mortarboard-fill',
      'annual_price'     => (int) $request->get('annual_price', 0),
      'annual_old_price' => (int) $request->get('annual_old_price', 0),
      'monthly_price'    => (int) $request->get('monthly_price', 0),
      'ideal_text'       => $request->get('ideal_text'),
      'cta_text'         => $request->get('cta_text') ?: 'Choose Plan',
      'inherit_text'     => $request->get('inherit_text'),
      'features'         => $this->cleanFeatures($request->get('features')),
      'badge_type'       => $request->get('badge_type') ?: null,
      'badge_text'       => $request->get('badge_text') ?: null,
      'sort_order'       => (int) $request->get('sort_order', 0),
      'status'           => $request->get('status'),
      'updated_at'       => date('Y-m-d H:i:s'),
    ]);

    return Redirect::to("admin/subscription-plans")->withSuccess("Great! Subscription plan has been updated");
  }

  // Normalizes the features repeater input (features[N][text], features[N][enabled])
  // into a JSON string of [{text, enabled}, ...] rows, dropping blank rows.
  private function cleanFeatures($rows)
  {
    $rows = is_array($rows) ? $rows : [];

    $clean = [];
    foreach ($rows as $row) {
      $text = trim($row['text'] ?? '');
      if ($text === '') {
        continue;
      }
      $clean[] = [
        'text'    => $text,
        'enabled' => !empty($row['enabled']),
      ];
    }

    return json_encode($clean);
  }
}
