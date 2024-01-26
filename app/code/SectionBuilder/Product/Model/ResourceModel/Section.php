<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model\ResourceModel;

class Section extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'section_builder_product';
    const FILE_BASE_CSS = 'assets/bss-section-builder.css';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            self::MAIN_TABLE,
            \SectionBuilder\Product\Api\Data\SectionInterface::ID
        );
    }
}
