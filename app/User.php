<?php

namespace Vanguard;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Vanguard\Models\Agency;
use Vanguard\Models\Broadcaster;
use Vanguard\Models\Company;
use Vanguard\Presenters\UserPresenter;
use Vanguard\Services\Auth\TwoFactor\Authenticatable as TwoFactorAuthenticatable;
use Vanguard\Services\Auth\TwoFactor\Contracts\Authenticatable as TwoFactorAuthenticatableContract;
use Vanguard\Services\Logging\UserActivity\Activity;
use Vanguard\Support\Enum\UserStatus;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Models\Role;

class User extends Authenticatable implements TwoFactorAuthenticatableContract
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    use TwoFactorAuthenticatable, CanResetPassword, PresentableTrait, Notifiable, HasRoles;

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
        'id', 'name', 'email', 'password', 'username', 'firstname', 'lastname', 'phone', 'avatar',
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

    public function setBirthdayAttribute($value)
    {
        $this->attributes['birthday'] = trim($value) ?: null;
    }

    public function gravatar()
    {
        $hash = hash('md5', strtolower(trim($this->attributes['email'])));

        return sprintf("//www.gravatar.com/avatar/%s", $hash);
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

    public function socialNetworks()
    {
        return $this->hasOne(UserSocialNetworks::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    public function getCompanyTypeAttribute()
    {
        return $this->companies->first()->company_type->name;
    }

    public function getFullNameAttribute()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function getCompanyIdAttribute()
    {
        $company_ids = [];
        foreach ($this->companies as $company){
            $company_ids[] = $company->id;
        }
        return $company_ids;
    }

    public function getUserCompanyChannelsAttribute()
    {
        $channels_id = [];
        $user_companies = $this->companies;
        foreach ($user_companies as $user_company){
            if(!in_array($user_company->channels->first()->id, $channels_id)){
                array_push($channels_id, $user_company->channels->first()->id);
            }
        }
        return $channels_id;
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

    //temporary and will be removed when the proper legal entity is implemented
    public function broadcaster()
    {
        return $this->belongsTo(Broadcaster::class);
    }

    //temporary and will be removed when the proper legal entity is implemented
    public function agent()
    {
        return $this->belongsTo(Agency::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function assignRole($roles, string $guard = null)
    {
        $roles = \is_string($roles) ? [$roles] : $roles;
        $guard = $guard ? : $this->getDefaultGuardName();

        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) use ($guard) {
                return $this->getStoredRole($role, $guard);
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->all();

        $this->roles()->saveMany($roles);

        $this->forgetCachedPermissions();

        return $this;
    }

    protected function getStoredRole($role, string $guard): Role
    {
        if (\is_string($role)) {
            return app(Role::class)->findById($role, $guard);
        }

        return $role;
    }

    public function syncRoles($roles, $guard)
    {
        $this->roles()->detach();

        return $this->assignRole($roles, $guard);
    }
}
