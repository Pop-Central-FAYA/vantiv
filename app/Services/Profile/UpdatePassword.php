<?php

namespace Vanguard\Services\Profile;

use Vanguard\User;
use Illuminate\Support\Arr;
use DB;
use Vanguard\Services\BaseServiceInterface;

/**
 * This service is to update a user.
 */
class UpdatePassword implements BaseServiceInterface
{
    protected $user;
    protected $data;

    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Update the user  password
     */
    public function run()
    {
        return $this->update();
    }

    /**
     * Update the user password,
     * @return \Vanguard\User  The model holding the user
     */
    protected function update()
    {
        return DB::transaction(function () {
            if (Arr::has($this->data, 'password')) {
                $this->user->setAttribute('password', $this->data['password']);
            }
            $this->user->save();
            return $this->user;
        });
    }
}
