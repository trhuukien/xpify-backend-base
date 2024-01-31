<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Model\ResourceModel\CategoryProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Category\Model\ResourceModel\CategoryProduct::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Category\Model\CategoryProduct::class,
            \SectionBuilder\Category\Model\ResourceModel\CategoryProduct::class
        );
    }
}
