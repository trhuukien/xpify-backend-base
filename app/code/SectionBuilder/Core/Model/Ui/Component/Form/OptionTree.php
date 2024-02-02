<?php
namespace SectionBuilder\Core\Model\Ui\Component\Form;

class OptionTree
{
    protected $optionTree;

    protected function getOptionTree($collection, $filters = [], $default = [])
    {
        if ($this->optionTree === null) {
            $options = $default;

            foreach ($filters as $filter) {
                $collection->addFieldToFilter(
                    $filter['field'],
                    $filter['value'],
                    $filter['condition'] ?? 'eq'
                );
            }

            foreach ($collection as $item) {
                $id = $item->getData('entity_id');
                $options[$id]['value'] = $id;
                $options[$id]['label'] = $item->getName();
            }
            $this->optionTree = $options;
        }

        return $this->optionTree;
    }
}
