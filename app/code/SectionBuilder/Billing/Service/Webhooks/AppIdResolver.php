<?php
declare(strict_types=1);

namespace SectionBuilder\Billing\Service\Webhooks;

use Magento\Framework\Exception\LocalizedException;
use SectionBuilder\Core\Model\Config;
use Xpify\Webhook\Model\WebhookAppResolverInterface as IWebhookAppResolver;

class AppIdResolver implements IWebhookAppResolver
{
    private Config $configProvider;

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
    public function getAppId(): ?int
    {
        $appId = $this->configProvider->getAppConnectingId();
        if (!$appId) {
            throw new LocalizedException(__("Please config app connecting in Stores > Configuration > Shopify App > App > Connecting"));
        }
        return $appId;
    }
}
