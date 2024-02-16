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

    public function addRowUniqueKey($fieldToFilter, $fieldToAdd)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable());

        foreach ($fieldToFilter as $field => $value) {
            $select->where("$field = ?", $value);
        }

        if ($connection->fetchRow($select)) {
            $connection->update($this->getMainTable(), $fieldToAdd);
        } else {
            $fieldToAdd = array_merge($fieldToFilter, $fieldToAdd);
            $connection->insert($this->getMainTable(), $fieldToAdd);
        }

        return $connection;
    }
}
