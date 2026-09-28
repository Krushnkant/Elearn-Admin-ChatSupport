<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\ContactEnquiry;
use Mail;

class ContactController extends Controller
{
	/**
	 * "Get in Touch" enquiry. The enquiry is SAVED to contact_enquiries first —
	 * that record is the source of truth, visible in the admin panel. The
	 * notification email to the support inbox is best-effort: if SMTP is down
	 * or misconfigured the enquiry is still captured and the learner still
	 * gets a success. Reply-To is the sender, so answering is just hitting reply.
	 */
	public function send(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name'    => 'required|string|max:120',
			'email'   => 'required|email|max:150',
			'subject' => 'nullable|string|max:180',
			'message' => 'required|string|max:5000',
		]);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message' => $validator->errors()->first(),
			], 422);
		}

		$user    = $request->user();
		$name    = trim($request->input('name'));
		$email   = trim($request->input('email'));
		$subject = trim((string) $request->input('subject'));
		$body    = trim($request->input('message'));

		$enquiry = ContactEnquiry::create([
			'user_id' => $user ? $user->id : null,
			'name'    => $name,
			'email'   => $email,
			'subject' => $subject ?: null,
			'message' => $body,
		]);

		// Best-effort notification mail; the saved enquiry is the record.
		$to = config('mail.contact_to');
		if (!empty($to)) {
			$data = [
				'name'    => $name,
				'email'   => $email,
				'subject' => $subject,
				'body'    => $body,
				// Who was signed in, so a reply can be tied to the right account.
				'account' => $user ? trim($user->name . ' <' . $user->email . '> (user #' . $user->id . ')') : null,
			];

			try {
				Mail::send('mail.contact-enquiry', $data, function ($message) use ($to, $name, $email, $subject) {
					$message->to($to)
						->replyTo($email, $name)
						->subject('Website enquiry: ' . ($subject ?: 'General') . ' — ' . $name);
				});
				$enquiry->update(['mailed' => 1]);
			} catch (\Exception $e) {
				Log::error('Contact enquiry #' . $enquiry->id . ' saved, but notification mail failed: ' . $e->getMessage());
			}
		}

		if ($user) {
			\App\Models\Notification::notify(
				$user->id,
				'We received your message',
				"Thanks for reaching out! We've got your \"" . ($subject ?: 'General') . '" enquiry and will reply within 24 hours.',
				'general'
			);
		}

		return response()->json([
			'success' => true,
			'message' => 'Thanks for reaching out! We have got your message and will reply within 24 hours.',
		]);
	}
}
