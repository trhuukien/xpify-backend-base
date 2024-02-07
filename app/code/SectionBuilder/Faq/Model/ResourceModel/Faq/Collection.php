<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Model\ResourceModel\Faq;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \SectionBuilder\Faq\Api\Data\FaqInterface::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SectionBuilder\Faq\Model\Faq::class,
            \SectionBuilder\Faq\Model\ResourceModel\Faq::class
        );
    }
}
