<?php
declare(strict_types=1);

namespace Xpify\Webhook\Controller\Webhook;

use Magento\Framework\App\Action\HttpPostActionInterface as IHttpAction;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Xpify\Webhook\Service\Webhook as WebhookProcessor;

class Index implements IHttpAction, CsrfAwareActionInterface
{
    private WebhookProcessor $webhookProcessor;

    private JsonFactory $jsonFactory;

    /**
     * @param WebhookProcessor $webhookProcessor
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        WebhookProcessor $webhookProcessor,
        JsonFactory $jsonFactory
    ) {
        $this->webhookProcessor = $webhookProcessor;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(): Json
    {
        list($code, $msg) =$this->webhookProcessor->process();
        return $this->jsonFactory->create()->setData(['message' => $msg])->setHttpResponseCode($code);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
