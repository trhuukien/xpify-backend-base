<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model\Billing;

use Magento\Framework\Controller\ResultFactory;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as IMerchantSubscription;

/**
 * Interface SubscriptionSuccessResolverInterface
 * @since 1.0.1
 */
interface SubscriptionSuccessResolverInterface
{
    /**
     * Returns the sort order of the resolver
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Indicates if the resolver should be used to resolve the response
     *
     * @param IApp $app
     * @param IMerchant $merchant
     * @param IMerchantSubscription $subscription
     * @return bool
     */
    public function shouldResolve(IApp $app, Imerchant $merchant, IMerchantSubscription $subscription): bool;

    /**
     * Resolves the response
     *
     * @param IApp $app
     * @param IMerchant $merchant
     * @param IMerchantSubscription $subscription
     * @param ResultFactory $resultFactory
     * @return mixed
     */
    public function resolve(IApp $app, Imerchant $merchant, IMerchantSubscription $subscription, ResultFactory $resultFactory);
}
