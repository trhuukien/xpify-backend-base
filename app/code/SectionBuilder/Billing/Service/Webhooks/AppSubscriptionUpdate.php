<?php
declare(strict_types=1);

namespace SectionBuilder\Billing\Service\Webhooks;

use Shopify\Webhooks\Handler;
use Xpify\Core\Model\Logger;

class AppSubscriptionUpdate implements Handler
{

    /**
     * @inheritDoc
     */
    public function handle(string $topic, string $shop, array $body): void
    {
        $logger = Logger::getLogger('app_subscription_update.log');
        $logger->info(json_encode($body));
    }
}
