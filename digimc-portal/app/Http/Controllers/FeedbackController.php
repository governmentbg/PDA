<?php

namespace App\Http\Controllers;

use App\Enums\FeedbackCategoryEnum;
use App\Enums\SettingEnum;
use App\Http\Requests\CreateFeedbackRequest;
use App\Services\FeedbackService;
use Illuminate\Http\Request;
use Throwable;

class FeedbackController extends Controller
{
    public function store(CreateFeedbackRequest $request)
    {
        try {
            $feedbackService = new FeedbackService();
            $feedbackService->sendFeedback($request);

            return response()->json(['message' => __('feedback.modal.success'),]);
        } catch (Throwable $e) {
            report($e);
            return response()->json(['message' => __('feedback.modal.generic_error')], 500);
        }
    }
}
