<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppLanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'language_code' => $this->code,
            'language' => $this->name,
            'country_code' => $this->getCountryCode($this->flag_icon),
            'translation_keys' => file_exists(base_path('lang/' . $this->code . '/app_translation.json')) ?  json_decode(file_get_contents(base_path('lang/' . $this->code . '/app_translation.json')), true) : json_decode(file_get_contents(base_path('lang/en/app_translation.json')), true),
        ];
    }


    function getCountryCode($string)
        {
            // Match pattern like 'flag-icon-xx' and capture the country code
            if (preg_match('/flag-icon-([a-z]{2})\b/i', $string, $matches)) {
                return strtolower($matches[1]);
            }

            return 'US'; // return null if no match found
        }
}
