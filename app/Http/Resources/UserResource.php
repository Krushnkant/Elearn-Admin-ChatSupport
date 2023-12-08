<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TransactionResource;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            //'current_team_id' => $this->current_team_id,
            'profile_photo_path' => $this->profile_photo_path,
            //'role_id' => $this->role_id,
            //'status' => $this->status,
            'mobile_number' => $this->mobile_number,
            'nickname' => $this->nickname,
            'gender' => $this->gender,
            'bio' => $this->bio,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
            'active_plan' => $this->activeMembership() ? $this->activeMembership()->only('id','transaction_id', 'plan', 'amount', 'start_date', 'end_date') : (Object)[],
        ];
    }
}
