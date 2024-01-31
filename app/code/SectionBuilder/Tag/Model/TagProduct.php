<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Model;

class TagProduct extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SectionBuilder\Tag\Model\ResourceModel\TagProduct::class);
    }
}
