<?php
declare(strict_types=1);

namespace Xpify\Webhook\Model;

interface WebhookAppResolverInterface
{
    /**
     * This function will determine which app the registry webhook should register
     * Null is known as applying to all app
     *
     * @return int|null
     */
    public function getAppId(): ?int;
}
