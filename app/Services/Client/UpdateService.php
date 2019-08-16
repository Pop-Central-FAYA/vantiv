<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\Client;
use Vanguard\Models\ClientContact;
use Illuminate\Support\Arr;
use DB;
use Vanguard\Services\BaseServiceInterface;

/**
 * This service is to update a Client and contact.
 */
class UpdateService implements BaseServiceInterface
{
    const CLIENT_UPDATE_FIELDS = ['name', 'street_address', 'city', 'state', 'nationality'];
    const CLIENT_CONTACT_UPDATE_FIELDS = ['first_name', 'last_name', 'email', 'phone_number'];

    protected $client;
    protected $data;

    public function __construct($client, $data)
    {
        $this->client = $client;
        $this->data = $data;
    }

    /**
     * Validate the data (both input and business logic wise)
     * Update the client and the client contact
     */
    public function run()
    {
        return $this->update();
    }

    /**
     * Update the client, then update the vendor contact
     * @return \Vanguard\Models\Client  The model holding the vendor
     */
    protected function update()
    {
        return DB::transaction(function () {
            $this->updateModel($this->client, static::CLIENT_UPDATE_FIELDS, $this->data);

            $contact = Arr::get($this->data, 'contacts.0', []);
            $this->updateClientContact($contact, $this->client);

            return $this->client;
        });
    }

    /**
     * update the client contact information
     * @param  array $data The validated input data
     * @param  \Vanguard\Models\Client $client  The model holding the client information
     * @return \Vanguard\Models\ClientContact   The updated client contact
     */
    protected function updateClientContact(array $contact, Client $client)
    {
      
        $client_contact = $client->contacts->first();
        $this->updateModel($client_contact, static::CLIENT_CONTACT_UPDATE_FIELDS, $contact);
        return $client_contact;
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
