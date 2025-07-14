<?php namespace Boarworm\Restapi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        $project = $this->resource;

        return [
            'id' => $project->id,
//            'sort_order' => $project->???,
            'title' => $project->title,
            'slug' => $project->slug,
            'created_at' => $project->created_at,
            'is_enabled' => $project->is_enabled,
            'intro_text' => $project->intro_text,
            'image' => $project->image ? url($project->image->path) : null,
            'meta_title' => $project->meta_title,
            'meta_description' => $project->meta_description,
            'meta_keywords' => $project->meta_keywords,
        ];
    }
}
