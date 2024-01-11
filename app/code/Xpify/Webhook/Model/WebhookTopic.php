<?php
declare(strict_types=1);

namespace Xpify\Webhook\Model;

use Shopify\Webhooks\Handler as IHandler;
use Xpify\Webhook\Model\WebhookTopicInterface as IWebhookTopic;

class WebhookTopic implements IWebhookTopic
{
    /**
     * @var string
     */
    protected $topic;

    /**
     * @var IHandler
     */
    protected $handler;

    /**
     * WebhookTopic constructor.
     *
     * @param string $topic
     * @param IHandler $handler
     */
    public function __construct(string $topic, IHandler $handler)
    {
        $this->topic = $topic;
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @inheritDoc
     */
    public function getHandler(): IHandler
    {
        return $this->handler;
    }
}
