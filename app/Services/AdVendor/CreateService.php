<?php

namespace Vanguard\Services\AdVendor;

use Vanguard\Models\AdVendor as AdVendorModel;
use Vanguard\Models\AdVendorContact;
use DB;

/**
 * This service is to create a Vendor.
 * Creation of a Vendor will create both the Vendor and the VendorContact
 */
class CreateService
{
    protected $company_id;
    protected $data;
    protected $user_id;

    public function __construct($data, $company_id, $user_id)
    {
        $this->data = $data;
        $this->company_id = $company_id;
        $this->user_id = $user_id;
    }

    /**
     * Validate the data (both input and business logic wise)
     * Create the vendor and the vendor contact
     * @return [string] the model representing the created vendor
     */
    public function run()
    {
        return $this->store();
    }

    /**
     * Save the vendor, then save the vendor contact
     * @return \Vanguard\Models\AdVendor  The model holding the vendor
     */
    protected function store()
    {
        return DB::transaction(function () {
            $vendor = new AdVendorModel();
            $vendor->company_id = $this->company_id;
            $vendor->created_by = $this->user_id;
            $vendor->name = $this->data['name'];
            $vendor->street_address = $this->data['street_address'];
            $vendor->city = $this->data['city'];
            $vendor->state = $this->data['state'];
            $vendor->country = $this->data['country'];
            $vendor->save();

            $this->storeContact($this->data['contacts'], $vendor);
            return $vendor;
        });
    }

    /**
     * Save the vendor contact information
     * @param  array $data The validated input data
     * @param  \Vanguard\Models\AdVendor $vendor  The model holding the vendor information
     * @return \Vanguard\Models\AdVendorContact   The created vendor contact
     */
    protected function storeContact(array $contacts_list, AdVendorModel $vendor)
    {
        foreach ($contacts_list as $data) {
            $vendor_contact = new AdVendorContact();
            $vendor_contact->ad_vendor_id = $vendor->id;
            $vendor_contact->created_by = $vendor->created_by;
            $vendor_contact->first_name = $data['first_name'];
            $vendor_contact->last_name = $data['last_name'];
            $vendor_contact->email = $data['email'];
            $vendor_contact->phone_number = $data['phone_number'];
            $vendor_contact->is_primary = true;
            $vendor_contact->save();
        }
        return $vendor_contact;
    }
}
