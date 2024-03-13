<?php
declare(strict_types=1);

namespace SectionBuilder\Billing\Model\Billing;

use Magento\Framework\Controller\ResultFactory;
use SectionBuilder\Core\Model\Config;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as IMerchantSubscription;
use Xpify\Merchant\Model\Billing\SubscriptionSuccessResolverInterface as IResolver;

class SubscriptionSuccess implements IResolver
{
    private \SectionBuilder\Core\Model\Config $configProvider;

    /**
     * @param Config $configProvider
     */
    public function __construct(
        \SectionBuilder\Core\Model\Config $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function shouldResolve(IApp $app, IMerchant $merchant, IMerchantSubscription $subscription): bool
    {
        $sectionBuilderAppId = $this->configProvider->getAppConnectingId();
        return $app->getId() . "" === $sectionBuilderAppId . "";
    }

    /**
     * @inheritDoc
     */
    public function resolve(IApp $app, IMerchant $merchant, IMerchantSubscription $subscription, ResultFactory $resultFactory)
    {
        $returnUrl = "https://" . $merchant->getShop() . "/admin/apps/" . $app->getName() . "/plans/?billing_completed=1";
        return $resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($returnUrl);
    }
}
