<?php namespace Boarworm\Restapi\Repositories\Contracts;

use Tailor\Models\EntryRecord;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function getBySlug(string $slug): EntryRecord;

    public function getAll(): Collection;
}
