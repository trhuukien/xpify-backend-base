<?php
declare(strict_types=1);

namespace Xpify\MerchantGraphQl\Model;

use Xpify\Core\Helper\Utils;
use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as ISubscription;

class SubscriptionFormatter
{
    public function toGraphQlOutput(ISubscription $subscription): array
    {
        return array_merge($subscription->getData(), [ 'id' => Utils::idToUid((string) $subscription->getId()), 'model' => $subscription ]);
    }
}
