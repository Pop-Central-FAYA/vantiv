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
        dd($this->id);
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone_number' => $this->phone_number,
            'role_name' => $this->formatRole($this->getRoleNames()),
            'company_name' => $this->getCompanyName(),
            'company_id' => $this->company_id,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at->toDateTimeString(),
            'links'=> [
                'update' => route('users.update', ['id' => $this->id], false),
                'reinvite' => route('users.reinvite', ['id' => $this->id], false),
                'delete' => route('users.delete', ['id' => $this->id], false),
                'index' => route('users.index'),
                'profile_update' => route('profile.update', ['id' => $this->id], false),
            ],
        ];
    }

    public function formatRole($roles)
    {
        $role_list = [];
        foreach ($roles as $role){
        $single_role = (object)[
               'role' => $role,
               'label' => ucwords(str_replace('_',' ', explode('.', $role)[1])),
        ];
        array_push($role_list, $single_role);
        }
        return $role_list;
    }
}