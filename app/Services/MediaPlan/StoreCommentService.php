<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\Comment;

class StoreCommentService
{
    protected $user;
    protected $media_plan;
    protected $validated;

    public function __construct($user, $media_plan, $validated)
    {
        $this->user = $user;
        $this->media_plan = $media_plan;
        $this->validated = $validated;
    }

    public function run()
    {
        $new_comment = $this->user->comment($this->media_plan, $this->validated['comment']);
        $full_details = Comment::with(['user'])->where('id', $new_comment->id)->where('commentable_type', 'Vanguard\Models\MediaPlan')->first();
        return $full_details;
    }
}