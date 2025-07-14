<?php namespace Boarworm\Restapi\Repositories\Eloquent;

use Boarworm\Restapi\dtos\SiteSettingsDto;
use Boarworm\Restapi\Repositories\Contracts\SiteSettingsRepositoryInterface;
use Tailor\Models\GlobalRecord;

class SiteSettingsRepository implements SiteSettingsRepositoryInterface
{
    protected object $model;

    public function __construct(GlobalRecord $globalRecord)
    {
        $this->model = $globalRecord->inGlobal("Site\Config")->firstOrFail();
    }

    public function getAll(): SiteSettingsDto
    {
        return new SiteSettingsDto(
            emails: collect($this->model->emails ?? [])->pluck('email')->all(),
            phones: collect($this->model->phones ?? [])->pluck('phone')->all(),
            siteName: $this->model->site_name ?? null,
        );
    }
}
