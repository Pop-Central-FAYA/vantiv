<?php

namespace Vanguard\Services\Profile;

use Vanguard\User;
use Illuminate\Support\Arr;
use DB;
use Vanguard\Services\BaseServiceInterface;

/**
 * This service is to update a user.
 */
class UpdateService implements BaseServiceInterface
{
    const USER_UPDATE_FIELDS = ['firstname', 'lastname', 'phone_number', 'avatar'];

    protected $user;
    protected $data;

    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Update the user 
     */
    public function run()
    {
        return $this->update();
    }

    /**
     * Update the user,
     * @return \Vanguard\User  The model holding the user
     */
    protected function update()
    {
        return DB::transaction(function () {
            $this->updateModel($this->user, static::USER_UPDATE_FIELDS, $this->data);
            return $this->user;
        });
    }

    /**
     * Setting attributes like this, so that events are fired
     * if we just do a model update directly from array, events will not be fired
     */
    private function updateModel($model, $update_fields, $data)
    {
        foreach ($update_fields as $key) {
            if (Arr::has($data, $key)) {
                $model->setAttribute($key, $data[$key]);
            }
        }
        //save will only actually save if the model has changed
        $model->save();
    }
}
