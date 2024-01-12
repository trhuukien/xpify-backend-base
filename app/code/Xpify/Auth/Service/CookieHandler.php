<?php
declare(strict_types=1);

namespace Xpify\Auth\Service;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Shopify\Auth\OAuthCookie;
use Shopify\Context;

class CookieHandler
{
    private CookieManagerInterface $cookieManager;

    private CookieMetadataFactory $cookieMetadataFactory;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function saveShopifyCookie(OAuthCookie $cookie)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($cookie->getExpire() ? ceil(($cookie->getExpire() - time()) / 60) : null) // Cookie will expire after one day (86400 seconds)
            ->setSecure($cookie->isSecure())
            ->setPath('/')
            ->setHttpOnly($cookie->isHttpOnly())
            ->setSameSite('Lax')
            ->setDomain(parse_url(Context::$HOST_SCHEME . "://" . Context::$HOST_NAME, PHP_URL_HOST));

        $this->cookieManager->setPublicCookie(
            $cookie->getName(),
            $cookie->getValue(),
            $metadata
        );
    }
}
