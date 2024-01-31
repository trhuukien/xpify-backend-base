<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Model\ResourceModel;

class CategoryProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'section_builder_category_product';
    const ID = 'entity_id';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            self::MAIN_TABLE,
            self::ID
        );
    }
}
