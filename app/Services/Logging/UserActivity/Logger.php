<?php

namespace Vanguard\Services\Logging\UserActivity;

use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class Logger
{
    /**
     * @var Request
     */
    private $request;
   
  
    /**
     * @var ActivityRepository
     */
    private $activities;

    public function __construct(Request $request,  ActivityRepository $activities)
    {
        $this->request = $request;
        $this->activities = $activities;
    }

    /**
     * Log user action.
     *
     * @param $description
     * @return static
     */
    public function log($description, $user)
    {
        return $this->activities->log([
            'description' => $description,
            'user_id' => $user->id,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->getUserAgent()
        ]);
    }

    

    /**
     * Get user agent from request headers.
     *
     * @return string
     */
    private function getUserAgent()
    {
        return substr((string) $this->request->header('User-Agent'), 0, 500);
    }

    /**
     * @param User|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}