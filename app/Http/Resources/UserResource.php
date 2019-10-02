<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vanguard\Services\User\FormatUserList;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $format = new FormatUserList();
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone_number' => $this->phone_number,
            'roles' => $format->roleLabel($this->getRoleNames()),
            'role_name' => $format->formatLable($this->getRoleNames()),
            'company' => $format->getCompanyName($this->company_id),
            'company_id' => $this->company_id,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'address' => $this->address,
            'created_at' => $this->created_at->toDateTimeString(),
            'links'=> [
                'update' => route('users.update', ['id' => $this->id], false),
                'reinvite' => route('users.reinvite', ['id' => $this->id], false),
                'delete' => route('users.delete', ['id' => $this->id], false),
                'index' => route('users.index'),
            ],
        ];
    }
}