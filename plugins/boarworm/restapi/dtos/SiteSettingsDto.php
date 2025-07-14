<?php namespace Boarworm\Restapi\Dtos;

class SiteSettingsDto
{
    public function __construct(
        public readonly array $emails,
        public readonly array $phones,
        public readonly ?string $siteName,
    )
    {
    }
}
