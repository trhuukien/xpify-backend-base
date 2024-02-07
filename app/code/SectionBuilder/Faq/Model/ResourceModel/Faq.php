<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Model\ResourceModel;

class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'section_builder_faq';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            self::MAIN_TABLE,
            \SectionBuilder\Faq\Api\Data\FaqInterface::ID
        );
    }
}
