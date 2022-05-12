<?php

namespace App\Http\Resources;

use App\Enum\UserRole;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IncidentResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'incidents' => $this->when($this->isParticipantWithRole($request->user(), UserRole::MANAGER),IncidentResource::collection($this->incidents)),
            'participants' => UserResource::collection($this->users()->where('role_id', UserRole::PARTICIPANT)->get()),
            'managers' => UserResource::collection($this->users()->where('role_id', UserRole::MANAGER)->get()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'project_id' => $this->project_id,
        ];
    }
}
