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
            'roles' => $format->roleLabel($this->getRoleNames()),
            'role_name' => $this->getRoleNames(),
            'company' => $format->getCompanyName($this->company_id),
            'company_id' => $this->company_id,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }
}