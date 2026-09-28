<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
	/**
	 * Active subscription plans for the student Membership/pricing page,
	 * ordered for display. `features` is [{text, enabled}, ...] - `enabled`
	 * false means the feature renders crossed-out (explicitly not included
	 * in that plan) rather than being omitted.
	 */
	public function index(Request $request)
	{
		$plans = SubscriptionPlan::where('status', 1)
			->orderBy('sort_order', 'asc')
			->get()
			->map(function ($p) {
				return [
					'id'               => $p->id,
					'theme'            => $p->theme,
					'name'             => $p->name,
					'icon'             => $p->icon,
					'annual_price'     => (int) $p->annual_price,
					'annual_old_price' => (int) $p->annual_old_price,
					'monthly_price'    => (int) $p->monthly_price,
					'ideal_text'       => $p->ideal_text,
					'cta_text'         => $p->cta_text,
					'inherit_text'     => $p->inherit_text,
					'features'         => $p->feature_list,
					'badge_type'       => $p->badge_type,
					'badge_text'       => $p->badge_text,
				];
			});

		return response()->json([
			'success' => true,
			'message' => "Data successfully found.",
			'data'    => $plans,
		]);
	}
}
