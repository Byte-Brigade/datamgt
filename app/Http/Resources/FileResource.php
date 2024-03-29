<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'uploaded_by' => $this->user->name,
            'table_name' => $this->table_name,
            'filename' => $this->filename,
            'path' => $this->path,
            'status' => $this->status,
        ];
    }
}
