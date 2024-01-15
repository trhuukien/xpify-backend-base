<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model;

use Magento\Framework\App\RequestInterface as IRequest;
use Shopify\Auth\Session;
use Shopify\Context;
use Shopify\Utils;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Auth\Service\AuthRedirection;
use Xpify\AuthGraphQl\Exception\GraphQlShopifyReauthorizeRequiredException;
use Xpify\Merchant\Service\MerchantStorage;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

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

    /**
     * @param IRequest $request
     * @param AuthRedirection $authRedirection
     * @param MerchantStorage $merchantStorage
     * @param GetCurrentApp $getCurrentApp
     */
    public function __construct(
        IRequest $request,
        AuthRedirection $authRedirection,
        MerchantStorage $merchantStorage,
        GetCurrentApp $getCurrentApp
    ) {
        $this->request = $request;
        $this->authRedirection = $authRedirection;
        $this->merchantStorage = $merchantStorage;
        $this->getCurrentApp = $getCurrentApp;
    }
    /**
     * @inheritDoc
     * @throws GraphQlShopifyReauthorizeRequiredException
     */
    public function execute()
    {
        if ($this->hasInitialized) {
            return;
        }
        $isOnline = IApp::DEFAULT_ACCESS_MODE === IApp::ACCESS_MODE_ONLINE;

        /** @var IApp $app */
        $app = $this->getCurrentApp->get();

        if (!$app) {
            throw new \Exception("Something went wrong! Please contact us for support.");
        }
        $shop = Utils::sanitizeShopDomain($this->request->getParam('shop', ''));
        $session = Utils::loadCurrentSession($this->request->getHeaders()->toArray(), $_COOKIE, $isOnline);

        if ($session && $shop && $session->getShop() !== $shop) {
            // This request is for a different shop. Go straight to login
            $reauthorizeUrl = $this->authRedirection->createRedirectUrl($app, $shop);
            $e = new GraphQlShopifyReauthorizeRequiredException(__("Please reauthorize the app for $shop"));
            $e->setReauthorizeUrl($reauthorizeUrl);
            throw $e;
        }

        if ($session && $session->isValid()) {
            $authorizedMerchant = $this->merchantStorage->loadMerchantBySessionid($session->getId());
            // make a request to ensure the access token still valid. otherwise, re-authenticate the user.
            $response = $authorizedMerchant->getGraphQlClient()->query(static::TEST_GRAPHQL_QUERY);
            $proceed = $response->getStatusCode() === 200;
            if ($proceed) {
                $this->merchant = $authorizedMerchant;
                $this->session = $session;
            }
            $this->hasInitialized = true;
            return;
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
        $e = new GraphQlShopifyReauthorizeRequiredException(__("Please reauthorize the app for $shop"));
        $e->setReauthorizeUrl($redirectUrl);
        throw $e;
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
