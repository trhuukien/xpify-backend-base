<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model\ResourceModel\SectionInstall;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Product\Api\Data\SectionInstallInterface::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Product\Model\SectionInstall::class,
            \SectionBuilder\Product\Model\ResourceModel\SectionInstall::class
        );
    }
}
