<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle_number' => $this->vehicle_number,
            'status' => $this->status,
            'entry_time' => $this->entry_time,
            'exit_time' => $this->exit_time,
        ];
    }

}
