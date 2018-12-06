<?php

namespace Vanguard\Repositories\User;

use Vanguard\User;

interface UserRepository
{

    /**
     * Find user by its id.
     *
     * @param $id
     * @return null|User
     */
    public function find($id);

    /**
     * Find user by email.
     *
     * @param $email
     * @return null|User
     */
    public function findByEmail($email);

    /**
     * Update user specified by it's id.
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);


    /**
     * Find user by confirmation token.
     *
     * @param $token
     * @return mixed
     */
    public function findByConfirmationToken($token);
}