<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Service;

use Xpify\Core\Exception\ShopifyQueryException;
use Xpify\Merchant\Helper\GraphqlQueryTrait;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;


final class Subscription
{
    use GraphqlQueryTrait;

    /**
     * Get subscription by name
     *
     * @param IMerchant $merchant
     * @param string $planName
     * @return array - structure of the subscription: id, name, test, currentPeriodEnd, trialDays
     * @throws ShopifyQueryException
     */
    public static function getSubscriptionByName(IMerchant $merchant, string $planName): array
    {
        $responseBody = self::query($merchant, self::RECURRING_PURCHASES_QUERY);
        $subscriptions = $responseBody["data"]["currentAppInstallation"]["activeSubscriptions"];
        foreach ($subscriptions as $subscription) {
            if ($subscription["name"] === $planName) {
                return $subscription;
            }
        }
        return [];
    }

    private const RECURRING_PURCHASES_QUERY = <<<'QUERY'
    query appSubscription {
        currentAppInstallation {
            activeSubscriptions {
                id
                name
                test
                currentPeriodEnd
                trialDays
            }
        }
    }
    QUERY;
}
