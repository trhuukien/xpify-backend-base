<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model\Billing;

use Magento\Framework\Controller\ResultFactory;
use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as IMerchantSubscription;
use Xpify\Merchant\Model\Billing\SubscriptionSuccessResolverInterface as IResolver;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\App\Api\Data\AppInterface as IApp;

/**
 * Class SuccessResponseResolver
 * @since 1.0.1
 */
class SuccessResponseResolver implements IResolver
{
    private int $sortOrder;

    public function __construct(int $sortOrder = 9999) {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @inheritDoc
     */
    public function resolve(IApp $app, Imerchant $merchant, IMerchantSubscription $subscription, ResultFactory $resultFactory)
    {
        $returnUrl = "https://" . $merchant->getShop() . "/admin/apps/" . $app->getHandle() . "/?billing_completed=1";
        return $resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($returnUrl);
    }

    /**
     * Default resolver should always resolve
     */
    public function shouldResolve(IApp $app, Imerchant $merchant, IMerchantSubscription $subscription): bool
    {
        return true;
    }
}
