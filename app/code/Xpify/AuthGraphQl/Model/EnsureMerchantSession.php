<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model;

use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Psr\Log\LoggerInterface;
use Shopify\Auth\Session;
use Shopify\Context;
use Shopify\Utils;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Auth\Service\AuthRedirection;
use Xpify\AuthGraphQl\Exception\GraphQlShopifyReauthorizeRequiredException;
use Xpify\Core\Model\Constants;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\Merchant\Exception\ShopifyBillingException;
use Xpify\Merchant\Service\Billing;
use Xpify\Merchant\Service\MerchantStorage;

class EnsureMerchantSession
{
    private bool $hasInitialized = false;
    private ?IMerchant $merchant = null;
    private ?Session $session = null;

    public const TEST_GRAPHQL_QUERY = <<<QUERY
    {
        shop {
            name
        }
    }
    QUERY;

    private IRequest $request;
    private AuthRedirection $authRedirection;
    private MerchantStorage $merchantStorage;
    private GetCurrentApp $getCurrentApp;
    private \Psr\Log\LoggerInterface $logger;
    private Billing $billing;

    /**
     * @param IRequest $request
     * @param AuthRedirection $authRedirection
     * @param MerchantStorage $merchantStorage
     * @param GetCurrentApp $getCurrentApp
     * @param LoggerInterface $logger
     * @param Billing $billing
     */
    public function __construct(
        IRequest $request,
        AuthRedirection $authRedirection,
        MerchantStorage $merchantStorage,
        GetCurrentApp $getCurrentApp,
        \Psr\Log\LoggerInterface $logger,
        Billing $billing
    ) {
        $this->request = $request;
        $this->authRedirection = $authRedirection;
        $this->merchantStorage = $merchantStorage;
        $this->getCurrentApp = $getCurrentApp;
        $this->logger = $logger;
        $this->billing = $billing;
    }
    /**
     * @throws GraphQlShopifyReauthorizeRequiredException|LocalizedException
     * @since 1.0.0
     */
    public function execute(): void
    {
        if ($this->hasInitialized) {
            return;
        }
        $isOnline = IApp::DEFAULT_ACCESS_MODE === IApp::ACCESS_MODE_ONLINE;

        /** @var IApp $app */
        $app = $this->getCurrentApp->get();

        if (!$app) {
            $this->logger->debug(__("App must be initialized before merchant session can be initialized. Let check the module sequence."));
            throw new LocalizedException(__(Constants::INTERNAL_SYSTEM_ERROR_MESS));
        }
        $shop = Utils::sanitizeShopDomain($this->request->getParam('shop', ''));
        $session = Utils::loadCurrentSession($this->request->getHeaders()->toArray(), $_COOKIE, $isOnline);

        if ($session && $shop && $session->getShop() !== $shop) {
            // This request is for a different shop. Go straight to login
            $reauthorizeUrl = $this->authRedirection->createRedirectUrl($app, $shop);
            throw new GraphQlShopifyReauthorizeRequiredException(__("Please reauthorize the app for $shop"), null, 0, true, $reauthorizeUrl);
        }

        if ($session && $session->isValid()) {
            $authorizedMerchant = $this->merchantStorage->loadMerchantBySessionid($session->getId());
            try {
                list($shouldPay, $payUrl) = $this->billing->check($session);
            } catch (ShopifyBillingException|NoSuchEntityException $e) {
                throw $e;
            } catch (\Exception $e) {
                $this->logger->debug($e);
                throw new LocalizedException(__(Constants::INTERNAL_SYSTEM_ERROR_MESS));
            }
            if ($shouldPay) {
                throw new GraphQlShopifyReauthorizeRequiredException(__("Payment required."), null, 0, true, $payUrl);
            } else {
                // make a request to ensure the access token still valid. otherwise, re-authenticate the user.
                try {
                    $response = $authorizedMerchant->getGraphql()->query(static::TEST_GRAPHQL_QUERY);
                } catch (\Throwable $e) {
                    $this->logger->debug($e);
                    // Unknown error. just throw error
                    throw new LocalizedException(__(Constants::INTERNAL_SYSTEM_ERROR_MESS));
                }
                $proceed = $response->getStatusCode() === 200;
            }
            if ($proceed) {
                $this->merchant = $authorizedMerchant;
                $this->session = $session;
                $this->hasInitialized = true;
                return;
            }
        }

        $authTokenHeader =$this->request->getHeader('Authorization', '');
        $bearerPresent = preg_match("/Bearer (.*)/", $authTokenHeader, $bearerMatches);
        if (!$shop) {
            if ($session) {
                $shop = $session->getShop();
            } elseif (Context::$IS_EMBEDDED_APP) {
                if ($bearerPresent !== false) {
                    $payload = Utils::decodeSessionToken($bearerMatches[1]);
                    $shop = parse_url($payload['dest'], PHP_URL_HOST);
                }
            }
        }

        $redirectUrl = $this->authRedirection->createAuthUrl($shop, (int) $app->getId());
        throw new GraphQlShopifyReauthorizeRequiredException(__("Please reauthorize the app for $shop"), null, 0, true, $redirectUrl);
    }

    public function hasInitialized(): bool
    {
        return $this->hasInitialized;
    }

    public function getMerchant(): ?IMerchant
    {
        return $this->merchant;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }
}
