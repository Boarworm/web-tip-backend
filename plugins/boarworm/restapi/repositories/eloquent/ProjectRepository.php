<?php namespace Boarworm\Restapi\Repositories\Eloquent;

use Boarworm\Restapi\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tailor\Models\EntryRecord;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected $model;

    public function __construct(EntryRecord $entryRecord)
    {
        $this->model = $entryRecord;
    }

    public function getAll(): Collection
    {
        return $this->model->inSection("Portfolio\Item")->get();
    }

    public function getById(int $id): EntryRecord
    {
        $project = $this->model->inSection("Portfolio\Item")->find($id);

        if (!$project) {
            throw new ModelNotFoundException('Project not found');
        }

        return $project;
//        return $this->model->inSection("Portfolio\Item")->findOrFail($id);
    }
}
