<?php

namespace Vanguard\Services\AdVendor;

use Vanguard\Models\AdVendorContact;
use DB;
use Vanguard\Services\BaseServiceInterface;

/**
 * This service is to create a Vendor contact
 */
class CreateContactService implements BaseServiceInterface
{
    protected $vendor_id;
    protected $data;
    protected $user_id;

    public function __construct($data, $vendor_id, $user_id)
    {
        $this->data = $data;
        $this->vendor_id = $vendor_id;
        $this->user_id = $user_id;
    }

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
            $vendor_contact = new AdVendorContact();
            $vendor_contact->ad_vendor_id = $this->vendor_id;
            $vendor_contact->created_by = $this->user_id;
            $vendor_contact->first_name = $this->data['first_name'];
            $vendor_contact->last_name = $this->data['last_name'];
            $vendor_contact->email = $this->data['email'];
            $vendor_contact->phone_number = $this->data['phone_number'];
            $vendor_contact->is_primary = true;
            $vendor_contact->save();

            return $vendor_contact;
        });
    }

}
