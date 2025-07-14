<?php namespace Boarworm\Restapi\Repositories\Eloquent;

use Boarworm\Restapi\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tailor\Models\EntryRecord;

class PostRepository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(EntryRecord $entryRecord)
    {
        $this->model = $entryRecord;
    }

    public function getAll(): Collection
    {
        return $this->model->inSection("Blog\Post")->get();
    }

    public function getBySlug(string $slug): EntryRecord
    {
        $post = $this->model->inSection("Blog\Post")->where('slug', $slug)->with('containers')->first();

        if (!$post) {
            throw new ModelNotFoundException('Post not found');
        }

        return $post;
    }
}
