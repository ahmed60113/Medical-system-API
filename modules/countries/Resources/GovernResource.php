<?php

namespace MEDICAL\Countries\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use MEDICAL\Countries\Resources\AreaResource;

class GovernResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;
        return [
                'id' => $resource->id,
                'arabicName' => $resource->name['ar'],
                'englishName' => $resource->name['en'],
                'areas' => AreaResource::collection($resource->children),

            
        ];
    }
}
