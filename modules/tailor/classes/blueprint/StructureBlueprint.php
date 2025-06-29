<?php namespace Tailor\Classes\Blueprint;

/**
 * StructureBlueprint
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class StructureBlueprint extends EntryBlueprint
{
    /**
     * @var string typeName of the blueprint
     */
    protected $typeName = 'structure';

    /**
     * getModelClassName
     */
    public function getModelClassName()
    {
        return \Tailor\Models\StructureRecord::class;
    }

    /**
     * hasTree
     */
    public function hasTree(): bool
    {
        return $this->getMaxDepth() !== 1;
    }

    /**
     * getMaxDepth
     */
    public function getMaxDepth(): int
    {
        return (int) ($this->structure['maxDepth'] ?? 0);
    }
}
