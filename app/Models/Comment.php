<?php
namespace Vanguard\Models;

use Vanguard\User;
use Illuminate\Database\Eloquent\Model;
use Actuallymab\LaravelComment\Models\Comment as LaravelComment;

class Comment extends LaravelComment
{
    public function user()
    {
        return $this->belongsTo(User::class, 'commented_id');
    }
}