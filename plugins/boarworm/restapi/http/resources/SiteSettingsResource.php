<?php namespace Boarworm\Restapi\Http\Resources;

use Boarworm\Restapi\dtos\SiteSettingsDto;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingsResource extends JsonResource
{
    /** @var SiteSettingsDto */
    public $resource;

    public function toArray($request): array
    {
        return [
            'emails' => $this->resource->emails,
            'phones' => $this->resource->phones,
            'site_name' => $this->resource->siteName,
        ];
    }
}
