<?php

namespace Vanguard\Services\AdVendor;

use Vanguard\Models\AdVendor as AdVendorModel;
use Vanguard\Models\AdVendorContact;
use Illuminate\Support\Arr;
use DB;

/**
 * This service is to update a Vendor.
 * Update of a vendor might also include updating the vendor contact
 */
class UpdateService
{
    const VENDOR_UPDATE_FIELDS = ['name', 'street_address', 'city', 'state', 'country'];
    const VENDOR_CONTACT_UPDATE_FIELDS = ['first_name', 'last_name', 'email', 'phone_number'];

    protected $vendor;
    protected $data;

    public function __construct($vendor, $data)
    {
        $this->vendor = $vendor;
        $this->data = $data;
    }

    /**
     * Validate the data (both input and business logic wise)
     * Update the vendor and the vendor contact
     * @return [string] the model representing the updated vendor
     */
    public function run()
    {
        return $this->update();
    }

    /**
     * Update the vendor, then update the vendor contact
     * @return \Vanguard\Models\AdVendor  The model holding the vendor
     */
    protected function update()
    {
        return DB::transaction(function () {
            $this->updateModel($this->vendor, static::VENDOR_UPDATE_FIELDS, $this->data);

            $contact = Arr::get($this->data, 'contacts.0', []);
            $this->updateVendorContact($contact, $this->vendor);

            return $this->vendor;
        });
    }

    /**
     * update the vendor contact information
     * @param  array $data The validated input data
     * @param  \Vanguard\Models\AdVendor $vendor  The model holding the vendor information
     * @return \Vanguard\Models\AdVendorContact   The updated vendor contact
     */
    protected function updateVendorContact(array $contact, AdVendorModel $vendor)
    {
        $vendor_contact = $vendor->contacts->first();
        $this->updateModel($vendor_contact, static::VENDOR_CONTACT_UPDATE_FIELDS, $contact);
        return $vendor_contact;
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
