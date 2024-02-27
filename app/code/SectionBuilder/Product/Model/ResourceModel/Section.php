<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model\ResourceModel;

class Section extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'section_builder_product';
    const SEPARATION = ";";

    protected $dateTimeFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTimeFactory = $dateTimeFactory;
    }

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

    /**
     * Prepare data to be saved to database.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->dateTimeFactory->create()->gmtDate());
        }

        $object->setUpdatedAt($this->dateTimeFactory->create()->gmtDate());
        return parent::_beforeSave($object);
    }
}
