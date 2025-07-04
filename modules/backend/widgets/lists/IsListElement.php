<?php namespace Backend\Widgets\Lists;

use Backend\Classes\ListColumn;
use October\Rain\Element\Lists\ColumnDefinition;

/**
 * IsListElement defines all methods to satisfy the ListElement contract
 *
 * @see \October\Contracts\Element\ListElement
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait IsListElement
{
    /**
     * defineColumn adds a column to the list element
     */
    public function defineColumn(string $columnName, ?string $label = null): ColumnDefinition
    {
        return $this->allColumns[$columnName] = new ListColumn([
            'columnName' => $columnName,
            'label' => $label
        ]);
    }

    /**
     * addColumnsFromModel from the model
     */
    protected function addColumnsFromModel()
    {
        if (!$this->model || !method_exists($this->model, 'defineListColumns')) {
            return;
        }

        $this->model->defineListColumns($this);
    }
}
