<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\UserCollection;
use App\Enum\UserRole;

class ProjectResource extends JsonResource
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
            'activities' => ActivityResource::collection($this->activities),
            'participants' => new UserCollection($this->users()->where('role_id', UserRole::PARTICIPANT)->get()),
            'managers' => new UserCollection($this->users()->where('role_id', UserRole::MANAGER)->get()),
        ];
    }
}
