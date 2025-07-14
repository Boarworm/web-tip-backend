<?php namespace Boarworm\Restapi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        $post = $this->resource;

//        dd($post->containers);
//        dd($this->prepareContainers($post));

        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'created_at' => $post->created_at,
            'is_enabled' => $post->is_enabled,
            'containers' => $this->prepareContainers($post),
            'intro_text' => $post->intro_text,
            'image' => $post->image ? url($post->image->path) : null,
            'meta_title' => $post->meta_title,
            'meta_description' => $post->meta_description,
            'meta_keywords' => $post->meta_keywords,
        ];
    }

    private function prepareContainers($post)
    {
        return $post->containers->map(function ($container) {
            return [
                'id' => $container->id,
                'name' => $container->container_name,
                'container_style' => $container->container_style,
                'gap_y' => $container->gap_y,
                'css_classes' => $container->css_classes,
                'html_anchor' => $container->html_anchor,
                'margins' => json_encode($container->margins),
                'paddings' => json_encode($container->paddings),
                'bg_color' => $container->bg_color,
                'blocks' => $container->blocks->map(function ($block) {
                    return [
                        'id' => $block->id,
                        'text' => $block->text,
                    ];
                }),
            ];
        });
    }


}
