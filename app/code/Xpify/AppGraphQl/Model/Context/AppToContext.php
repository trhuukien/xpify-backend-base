<?php
declare(strict_types=1);

namespace Xpify\AppGraphQl\Model\Context;

use Magento\Framework\App\Config\ScopeConfigInterface as IScopeConfig;
use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\GraphQl\Model\Query\ContextParametersInterface;
use Magento\GraphQl\Model\Query\ContextParametersProcessorInterface;
use Psr\Log\LoggerInterface;
use Shopify\ApiVersion;
use Shopify\Context as ShopifyContext;
use Xpify\App\Api\AppRepositoryInterface as IAppRepository;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Merchant\Service\MerchantStorage;

class AppToContext implements ContextParametersProcessorInterface
{
    private IRequest $request;

    private Uid $uidEncoder;

    private IAppRepository $appRepository;

    private IScopeConfig $scopeConfig;

    private MerchantStorage $merchantStorage;

    private \Psr\Log\LoggerInterface $logger;

    /**
     * @param IRequest $request
     * @param Uid $uidEncoder
     * @param IAppRepository $appRepository
     * @param IScopeConfig $scopeConfig
     * @param MerchantStorage $merchantStorage
     * @param LoggerInterface $logger
     */
    public function __construct(
        IRequest $request,
        Uid $uidEncoder,
        IAppRepository $appRepository,
        IScopeConfig $scopeConfig,
        MerchantStorage $merchantStorage,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->uidEncoder = $uidEncoder;
        $this->appRepository = $appRepository;
        $this->scopeConfig = $scopeConfig;
        $this->merchantStorage = $merchantStorage;
        $this->logger = $logger;
    }

    /**
     * Add app info to context
     *
     * @param ContextParametersInterface $contextParameters
     * @return ContextParametersInterface
     */
    public function execute(ContextParametersInterface $contextParameters): ContextParametersInterface
    {
        // X-Xpify-App: Shopify App ID
        $xpifyAppHeader = $this->request->getHeader('X-Xpify-App');
        if (!$xpifyAppHeader) {
            return $contextParameters;
        }

        try {
            $decodedAppId = $this->uidEncoder->decode($xpifyAppHeader);
            if ($decodedAppId !== null) {
                $app = $this->appRepository->get($decodedAppId, IApp::REMOTE_ID);
                if (!$app->getId()) {
                    return $contextParameters;
                }

                $contextParameters->addExtensionAttribute('app', $app);

                $host = $this->scopeConfig->getValue('web/secure/base_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                ShopifyContext::initialize(
                    $app->getApiKey(),
                    $app->getSecretKey(),
                    $app->getScopes(),
                    $host,
                    $this->getMerchantStorage(),
                    ApiVersion::LATEST,
                    true,
                    false,
                    null,
                    '',
                    $this->logger
                );
            }
        } catch (\Exception $e) {

        }

        return $contextParameters;
    }

    /**
     * Get merchant storage object
     *
     * @return MerchantStorage
     */
    public function getMerchantStorage(): MerchantStorage
    {
        return $this->merchantStorage;
    }
}
