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

    /**
     * set public cookie
     */
    public function setPublicCookie($cookieName, $value)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(86400) // Cookie will expire after one day (86400 seconds)
            ->setSecure(true) //the cookie is only available under HTTPS
            ->setPath('/subfolder')// The cookie will be available to all pages and subdirectories within the /subfolder path
            ->setHttpOnly(false); // cookies can be accessed by JS

        $this->cookieManager->setPublicCookie(
            $cookieName,
            $value,
            $metadata
        );
    }

    public function saveShopifyCookie(OAuthCookie $cookie)
    {

    }
}
