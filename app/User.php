<?php

namespace Vanguard;

use Illuminate\Notifications\Notifiable;
use Vanguard\Presenters\UserPresenter;
use Vanguard\Services\Auth\TwoFactor\Authenticatable as TwoFactorAuthenticatable;
use Vanguard\Services\Auth\TwoFactor\Contracts\Authenticatable as TwoFactorAuthenticatableContract;
use Vanguard\Services\Logging\UserActivity\Activity;
use Vanguard\Support\Authorization\AuthorizationUserTrait;
use Vanguard\Support\Enum\UserStatus;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements TwoFactorAuthenticatableContract
{
    use TwoFactorAuthenticatable, CanResetPassword, PresentableTrait, AuthorizationUserTrait, Notifiable;

    protected $presenter = UserPresenter::class;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $dates = ['last_login', 'birthday'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'email', 'password', 'firstname', 'lastname', 'phone_number', 'avatar',
        'address', 'country_id', 'birthday', 'last_login', 'confirmation_token', 'status',
        'group_id', 'remember_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function isUnconfirmed()
    {
        return $this->status == UserStatus::UNCONFIRMED;
    }

    public function isActive()
    {
        return $this->status == UserStatus::ACTIVE;
    }

    public function isBanned()
    {
        return $this->status == UserStatus::BANNED;
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection()
    {
        // TODO: Implement getQueueableConnection() method.
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        // TODO: Implement resolveRouteBinding() method.
    }
}
