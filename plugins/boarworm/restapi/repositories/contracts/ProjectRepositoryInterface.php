<?php namespace Boarworm\Restapi\Repositories\Contracts;

use Tailor\Models\EntryRecord;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
    public function getById(int $id): EntryRecord;

    public function getAll(): Collection;
}
