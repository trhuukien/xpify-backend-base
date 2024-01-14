<?php
declare(strict_types=1);

namespace Xpify\Webhook\Service;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface as IRequest;
use Shopify\Clients\HttpHeaders;
use Shopify\Exception\InvalidWebhookException;
use Shopify\Webhooks\Registry;
use Xpify\App\Api\AppRepositoryInterface as IAppRepository;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Core\Helper\ShopifyContextInitializer;
use Xpify\Core\Helper\Utils;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

class Webhook
{
    const WEBHOOK_PATH = '/api/webhook';

    private IRequest $request;
    private IAppRepository $appRepository;
    private SearchCriteriaBuilder $criteriaBuilder;
    private ShopifyContextInitializer $contextInitializer;
    private GetCurrentApp $getCurrentApp;

    /**
     * @param IRequest $request
     * @param IAppRepository $appRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param ShopifyContextInitializer $contextInitializer
     * @param GetCurrentApp $getCurrentApp
     */
    public function __construct(
        IRequest $request,
        IAppRepository $appRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        ShopifyContextInitializer $contextInitializer,
        GetCurrentApp $getCurrentApp
    ) {
        $this->request = $request;
        $this->appRepository = $appRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->contextInitializer = $contextInitializer;
        $this->getCurrentApp = $getCurrentApp;
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
     * @param string $merchantDomain The merchant domain
     * @param string $accessToken
     * @param IApp $app The app object
     * @return bool
     */
    public function register(string $topic, string $merchantDomain, string $accessToken, IApp $app): bool
    {
        $shop = $merchantDomain;

        try {
            $this->contextInitializer->initialize($app);
            $response = Registry::register(static::WEBHOOK_PATH, $topic, $shop, $accessToken);
            if ($response->isSuccess()) {
                return true;
            }
            $this->getLogger()?->debug(__("Failed to register APP_UNINSTALLED webhook for shop $shop with response body: %1", print_r($response->getBody(), true))->render());
        } catch (\Throwable $e) {
            $this->getLogger()?->debug(__("Failed to register APP_UNINSTALLED webhook for shop $shop with response body: %1", $e)->render());
        }
        return false;
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
            $app = $this->appOrException();
            $this->contextInitializer->initialize($app);

            $this->getCurrentApp->set($app);
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
     * Phương thức này được sử dụng để tìm một ứng dụng phù hợp với chữ ký HMAC từ yêu cầu.
     * Đầu tiên, nó lấy danh sách tất cả các ứng dụng từ kho ứng dụng.
     * Nếu không tìm thấy ứng dụng nào, nó sẽ ném ra một ngoại lệ.
     * Sau đó, nó lặp qua từng ứng dụng và kiểm tra xem ứng dụng có khóa bí mật hay không.
     * Nếu ứng dụng có khóa bí mật, nó sẽ xác thực chữ ký HMAC từ yêu cầu so với khóa bí mật của ứng dụng.
     * Nếu chữ ký HMAC hợp lệ, nó đặt ứng dụng tìm thấy thành ứng dụng hiện tại và ngừng vòng lặp.
     * Nếu không tìm thấy ứng dụng sau khi lặp qua tất cả các ứng dụng, nó sẽ ném ra một ngoại lệ.
     * Cuối cùng, nó trả về ứng dụng tìm thấy.
     *
     * @throws \Exception nếu không tìm thấy ứng dụng hoặc không có ứng dụng nào phù hợp với chữ ký HMAC từ yêu cầu
     * @return IApp ứng dụng tìm thấy
     */
    protected function appOrException(): IApp
    {
        $appSearchResults = $this->appRepository->getList($this->criteriaBuilder->create());
        if ($appSearchResults->getTotalCount() === 0) {
            throw new \Exception("No app found!");
        }
        foreach ($appSearchResults->getItems() as $app) {
            if ($app->getSecretKey()) {
                $validSign = Utils::validateHmac([
                    'data' => $this->request->getContent(),
                    'hmac' => $this->request->getHeader(HttpHeaders::X_SHOPIFY_HMAC),
                    'raw' => true,
                    'encode' => true,
                ], $app->getSecretKey());
                if ($validSign) {
                    $foundApp = $app;
                    break;
                }
            }
        }
        if (!isset($foundApp)) {
            throw new \Exception("No app found!");
        }
        return $foundApp;
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
