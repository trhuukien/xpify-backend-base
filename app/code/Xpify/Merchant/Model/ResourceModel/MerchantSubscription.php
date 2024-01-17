<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as IMerchantSubscription;

class MerchantSubscription extends AbstractDb
{
    const MAIN_TABLE = '$xpify_merchant_subscription';

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, IMerchantSubscription::ID);
    }
}
