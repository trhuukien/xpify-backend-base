<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Model\ResourceModel\Tag;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Tag\Api\Data\TagInterface::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Tag\Model\Tag::class,
            \SectionBuilder\Tag\Model\ResourceModel\Tag::class
        );
    }
}
