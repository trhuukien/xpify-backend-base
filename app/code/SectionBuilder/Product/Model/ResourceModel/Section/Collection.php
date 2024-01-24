<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model\ResourceModel\Section;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Product\Api\Data\SectionInterface::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Product\Model\Section::class,
            \SectionBuilder\Product\Model\ResourceModel\Section::class
        );
    }
}
