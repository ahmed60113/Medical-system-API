<?php

namespace MEDICAL\Countries\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource=$this->resource;
        return [
                'id' =>         $resource->id,
                'arabicName' => $resource->arabicName,
                'englishName' => $resource->englishName,
        ];
    }
}
