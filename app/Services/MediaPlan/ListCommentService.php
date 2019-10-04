<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\Comment;

class ListCommentService
{
    protected $plan_id;

    public function __construct($plan_id)
    {
        $this->plan_id = $plan_id;  
    }

    public function run()
    {
        $comments = Comment::with(['user'])->where('commentable_id', $this->plan_id)->where('commentable_type', 'Vanguard\Models\MediaPlan')->get();
        return $comments;
    }
}