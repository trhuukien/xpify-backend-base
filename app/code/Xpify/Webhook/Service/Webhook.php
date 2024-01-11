<?php
declare(strict_types=1);

namespace Xpify\Webhook\Service;

use Magento\Framework\App\RequestInterface as IRequest;
use Shopify\Clients\HttpHeaders;
use Shopify\Context;
use Shopify\Webhooks\Registry;
use Shopify\Exception\InvalidWebhookException;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

class Webhook
{
    const WEBHOOK_PATH = 'api/webhook';

    private IRequest $request;

    /**
     * @param IRequest $request
     */
    public function __construct(
        IRequest $request,
    ) {
        $this->request = $request;
    }

    /**
     * Register a webhook
     *
     * This method is used to register a webhook for a given topic. It first retrieves the shop and access token from the merchant object.
     * Then it tries to register the webhook using the Registry::register method. If the registration is successful, it logs a success message.
     * If the registration fails, it logs a failure message.
     * If an exception is caught, it logs an error message.
     *
     * @param string $topic The topic to register the webhook for
     * @param IMerchant $merchant The merchant object
     * @param IApp $app The app object
     * @return void
     */
    public function register(string $topic, IMerchant $merchant, IApp $app): void
    {
        $shop = $merchant->getShop();
        $accessToken = $merchant->getAccessToken();

        try {
            $response = Registry::register(static::WEBHOOK_PATH, $topic, $shop, $accessToken);
            if (!$response->isSuccess()) {
                $this->getLogger()?->debug(__("Failed to register APP_UNINSTALLED webhook for shop $shop with response body: %1", print_r($response->getBody(), true))->render());
            }
        } catch (\Throwable $e) {
            $this->getLogger()?->debug(__("Failed to register APP_UNINSTALLED webhook for shop $shop with response body: %1", $e)->render());
        }
    }

    /**
     * Process the webhook request
     *
     * This method is used to process the incoming webhook request. It first retrieves the topic from the request header.
     * Then it tries to process the request using the Registry::process method. If the processing is successful, it sets the response code to 200 and a success message.
     * If the processing fails, it sets the response code to 500 and a failure message.
     * If an InvalidWebhookException is caught, it sets the response code to 401 and an error message indicating an invalid webhook request.
     * If any other exception is caught, it sets the response code to 500 and an error message indicating an exception occurred while handling the webhook.
     * In all cases, it logs the error message if a logger is available.
     * Finally, it returns an array containing the response code and the error message.
     *
     * @return array An array containing the response code and the error message
     */
    public function process(): array
    {
        $topic = $this->request->getHeader(HttpHeaders::X_SHOPIFY_TOPIC, '');
        try {
            // required load app before processing webhook

            $response = Registry::process($this->request->getHeaders(), $this->request->getContent());
            if (!$response->isSuccess()) {
                $this->getLogger()?->debug(__("Failed to process '$topic' webhook: %1", $response->getErrorMessage())->render());
                $code = 500;
                $errmsg = __("Failed to process '$topic' webhook");
            } else {
                $code = 200;
                $errmsg = __("Processed '$topic' webhook successfully");
            }
        } catch (InvalidWebhookException $e) {
            $this->getLogger()?->debug(__("Got invalid webhook request for topic '$topic': %2", $e->getMessage())->render());
            $code = 401;
            $errmsg = __("Got invalid webhook request for topic '$topic'");
        } catch (\Exception $e) {
            $this->getLogger()?->debug(__("Got an exception when handling '$topic' webhook: %1", $e->getMessage())->render());
            $code = 500;
            $errmsg = __("Got an exception when handling '$topic' webhook");
        }
        return [$code, $errmsg];
    }

    /**
     * Logger hehe
     *
     * @return \Zend_Log|null
     */
    private function getLogger(): ?\Zend_Log
    {
        try {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/webhook_process.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            return $logger;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
