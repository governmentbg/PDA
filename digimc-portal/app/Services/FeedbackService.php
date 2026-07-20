<?php

namespace App\Services;

use App\Enums\SettingEnum;
use App\Mail\FeedbackSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class FeedbackService
{
    /** @param array|Request $request */
    public function sendFeedback(array|Request $request): void
    {
        $data = is_array($request)
            ? $request
            : $request->only(['subject','category','description','contact_email','name']);

        $mailEnabled = (bool) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_MAIL_ENABLED);
        if (!$mailEnabled) {
            Log::info('[FeedbackService] Mail disabled by settings.');
            return;
        }

        $from = SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_FROM_CONTACT_EMAIL);
        $to = SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_TO_CONTACT_EMAIL);

        if (!$to) {
            throw new RuntimeException('FEEDBACK_MAIL_MISCONFIGURED');
        }

        try {
            $mailable = new FeedbackSubmitted(
                $data['subject'],
                $data['category'],
                $data['description'],
                $data['contact_email'],
                $data['name']
            );

            if (!empty($from)) {
                $mailable->from($from);
            }

            Mail::to($to)->send($mailable);
        } catch (Throwable $e) {
            throw new RuntimeException('FEEDBACK_MAIL_FAILED', previous: $e);
        }
    }

}
