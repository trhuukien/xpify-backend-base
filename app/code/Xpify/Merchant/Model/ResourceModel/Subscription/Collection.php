<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model\ResourceModel\Subscription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = \Xpify\Merchant\Api\Data\MerchantSubscriptionInterface::ID;

    protected function _construct()
    {
        $this->_init(
            \Xpify\Merchant\Model\MerchantSubscription::class,
            \Xpify\Merchant\Model\ResourceModel\MerchantSubscription::class
        );
    }
}
