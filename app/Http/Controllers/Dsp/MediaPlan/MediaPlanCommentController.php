<?php

namespace Vanguard\Http\Controllers\Dsp\MediaPlan;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\MediaPlan;
use Vanguard\Services\MediaPlan\ListCommentService;
use Vanguard\Services\MediaPlan\StoreCommentService;
use Vanguard\Http\Resources\MediaPlanCommentCollection;
use Vanguard\Http\Requests\MediaPlan\StoreCommentRequest;
use Vanguard\Http\Resources\MediaPlanCommentResource;

class MediaPlanCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.media_plan')->only(['createComment', 'getComments']);
    }

    public function createComment(StoreCommentRequest $request, $media_plan_id) 
    {
        $validated = $request->validated();
        $user = auth()->user();
        $media_plan = MediaPlan::findorfail($media_plan_id);
        $comment = (new StoreCommentService($user, $media_plan, $validated))->run();
        return new MediaPlanCommentResource($comment);
    }

    public function getComments($id) 
    {
        $media_plan = MediaPlan::findorfail($id);
        $comments = (new ListCommentService($id))->run();
        return new MediaPlanCommentCollection($comments);
    }
}
