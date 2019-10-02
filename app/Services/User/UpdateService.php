<?php

namespace Vanguard\Services\User;

use Vanguard\User;

use Vanguard\Services\BaseServiceInterface;
use Illuminate\Support\Arr;

class UpdateService implements BaseServiceInterface
{
    protected $validated;
    protected $companies;
    protected $user_id;
    protected $guard;

    public function __construct($validated, $companies, $user_id, $guard)
    {
        $this->validated = $validated;
        $this->companies = $companies;
        $this->user_id = $user_id;
        $this->guard = $guard;
    }

    public function run()
    {
        return $this->updateUser();
    }

    private function updateUser()
    {
        $roles = Arr::pluck($this->validated['role_name'], 'role');
        $user = User::findOrFail($this->user_id);
        \DB::transaction(function () use ($user, $roles) {
        $user->syncRoles($roles, $this->guard);
        });
        return $user;
    }

}
