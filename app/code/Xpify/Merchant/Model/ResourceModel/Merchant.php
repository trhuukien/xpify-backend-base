<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model\ResourceModel;

use Magento\Framework\Exception\CouldNotDeleteException;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

class Merchant extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'xpify_merchants';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, IMerchant::ID);
    }

    /**
     * Delete any previously created OAuth sessions that were not completed (don't have an access token)
     *
     * @param string $shop
     * @return int|string
     * @throws CouldNotDeleteException
     */
    public function cleanNotCompleted(string $shop): int|string
    {
        try {
            $select = $this->getConnection()->select();
            $select->where(IMerchant::SHOP . " = ?", $shop)->where(sprintf("%s IS NULL", IMerchant::ACCESS_TOKEN));
            return $this->getConnection()->deleteFromSelect($select, $this->getMainTable());
        } catch (\Throwable $e) {
            throw new CouldNotDeleteException(__("Could not delete not completed merchant."));
        }
    }
}
