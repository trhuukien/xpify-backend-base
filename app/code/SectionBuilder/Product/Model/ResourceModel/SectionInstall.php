<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model\ResourceModel;

class SectionInstall extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'section_builder_install';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            self::MAIN_TABLE,
            \SectionBuilder\Product\Api\Data\SectionInstallInterface::ID
        );
    }
}