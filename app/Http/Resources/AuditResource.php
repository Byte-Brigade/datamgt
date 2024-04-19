<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
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
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'event' => $this->event,
            'subject_id' => $this->subject_id,
            'causer' => $this->causer->name,
            'properties' => $this->properties,
            'batch_uuid' => $this->batch_uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
