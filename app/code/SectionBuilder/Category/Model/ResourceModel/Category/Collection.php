<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Category\Api\Data\CategoryInterface::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Category\Model\Category::class,
            \SectionBuilder\Category\Model\ResourceModel\Category::class
        );
    }
}
