<?php

namespace Vanguard\Repositories\User;

use Vanguard\Services\Upload\UserAvatarManager;
use Vanguard\User;

class EloquentUser implements UserRepository
{
    /**
     * @var UserAvatarManager
     */
    private $avatarManager;
    /**
     * @var RoleRepository
     */

    public function __construct(UserAvatarManager $avatarManager)
    {
        $this->avatarManager = $avatarManager;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return User::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }



    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        if (! array_get($data, 'country_id')) {
            $data['country_id'] = null;
        }

        return $this->find($id)->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findByConfirmationToken($token)
    {
        return User::where('confirmation_token', $token)->first();
    }

}
