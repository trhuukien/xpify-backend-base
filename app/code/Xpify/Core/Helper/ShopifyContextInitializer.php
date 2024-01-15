<?php
declare(strict_types=1);

namespace Xpify\Core\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Shopify\ApiVersion;
use Shopify\Context as ShopifyContext;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Merchant\Service\MerchantStorage;

class ShopifyContextInitializer
{
    private LoggerInterface $logger;
    private ScopeConfigInterface $scopeConfig;
    private MerchantStorage $merchantStorage;

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param MerchantStorage $merchantStorage
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Xpify\Merchant\Service\MerchantStorage $merchantStorage
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->merchantStorage = $merchantStorage;
    }

    /**
     * Phương thức này được sử dụng để khởi tạo ngữ cảnh cho Shopify lib.
     *  Vì làm việc với nhiều ứng dụng, ta cần khởi tạo lại ngữ cảnh cho mỗi ứng dụng được yêu cầu.
     *
     * @param IApp $app
     * @return void
     * @throws \Shopify\Exception\MissingArgumentException
     */
    public function initialize(IApp $app): void
    {
        $host = $this->scopeConfig->getValue('web/secure/base_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        ShopifyContext::initialize(
            $app->getApiKey(),
            $app->getSecretKey(),
            $app->getScopes(),
            $host,
            $this->getMerchantStorage(),
            $app->getApiVersion() ? $app->getApiVersion() : ApiVersion::LATEST,
            true,
            false,
            null,
            '',
            $this->logger
        );
    }

    /**
     * Merchant storage service
     *
     * @return MerchantStorage
     */
    public function getMerchantStorage(): MerchantStorage
    {
        return $this->merchantStorage;
    }
}
