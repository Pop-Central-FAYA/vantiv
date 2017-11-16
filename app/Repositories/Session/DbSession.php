<?php

namespace Vanguard\Repositories\Session;

use Carbon\Carbon;
use Vanguard\Repositories\User\UserRepository;
use DB;

class DbSession implements SessionRepository
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * DbSession constructor.
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserSessions($userId)
    {
        $validTimestamp = Carbon::now()->subMinutes(config('session.lifetime'))->timestamp;

        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>=', $validTimestamp)
            ->get(['id', 'ip_address', 'user_agent', 'last_activity'])
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateUserSession($userId, $sessionId)
    {
        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', $sessionId)
            ->delete();

        $this->users->update($userId, ['remember_token' => null]);
    }
}