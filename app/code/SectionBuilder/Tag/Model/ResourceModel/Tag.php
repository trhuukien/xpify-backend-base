<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Model\ResourceModel;

class Tag extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'section_builder_tag';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            self::MAIN_TABLE,
            \SectionBuilder\Tag\Api\Data\TagInterface::ID
        );
    }
}
