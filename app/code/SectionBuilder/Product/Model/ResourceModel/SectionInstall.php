<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model\ResourceModel;

class SectionInstall extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'section_builder_install';

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
            \SectionBuilder\Product\Api\Data\SectionInstallInterface::ID
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

    public function replaceRow($condition, $fieldToAdd)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable());

        foreach ($condition as $field => $value) {
            $select->where("$field = ?", $value);
        }

        if ($connection->fetchRow($select)) {
            $connection->update($this->getMainTable(), $fieldToAdd, $condition);
        } else {
            $fieldToAdd = array_merge($condition, $fieldToAdd);
            $connection->insert($this->getMainTable(), $fieldToAdd);
        }

        return $connection;
    }

    public function deleteRow($condition)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), $condition);
        return $connection;
    }
}
