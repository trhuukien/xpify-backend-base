<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Model;

class CategoryProduct extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Category\Model\ResourceModel\CategoryProduct::class);
    }
}
