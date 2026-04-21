<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileAppSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'name' => $this->title,
            'slug' => $this->slug,
            'icon' => isset($this->upload->path)?@globalAsset($this->upload->path, '154X38.webp'): globalAsset($this->icon_path, '154X38.webp'),
        ];
    }
}
