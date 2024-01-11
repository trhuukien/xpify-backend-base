<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model\ResourceModel;

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
}
