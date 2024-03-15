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

    /**
     * Deactivate all subscriptions
     *
     * @param int $merchantId
     * @param int $appId
     * @return int
     */
    public function deactivateAllSubscriptions(int $merchantId, int $appId)
    {
        return $this->getConnection()->update(
            self::MAIN_TABLE,
            ['status' => IMerchantSubscription::STATUS_DEACTIVATED],
            [
                'merchant_id = ?' => $merchantId,
                'app_id = ?' => $appId,
                'status != ?' => IMerchantSubscription::STATUS_DEACTIVATED,
            ]
        );
    }
}
