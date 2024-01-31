<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Model\ResourceModel\TagProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Tag\Model\ResourceModel\TagProduct::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Tag\Model\TagProduct::class,
            \SectionBuilder\Tag\Model\ResourceModel\TagProduct::class
        );
    }
}
