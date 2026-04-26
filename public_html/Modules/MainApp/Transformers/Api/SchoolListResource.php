<?php

namespace Modules\MainApp\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SchoolListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => @globalAsset(setting('dark_logo'), '154X38.webp'),
            'api_url' => get_base_url($this->sub_domain_key).'/api',
        ];
    }
}
