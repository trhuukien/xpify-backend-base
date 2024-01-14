<?php
declare(strict_types=1);

namespace Xpify\Auth\Service;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Shopify\Auth\OAuthCookie;
use Shopify\Context;

class CookieHandler
{
    public static function saveShopifyCookie(OAuthCookie $cookie)
    {
        $metadata = \Magento\Framework\App\ObjectManager::getInstance()->create(\Magento\Framework\Stdlib\Cookie\PublicCookieMetadata::class, [])
            ->setDuration($cookie->getExpire() ? $cookie->getExpire() - time() : null)
            ->setSecure($cookie->isSecure())
            ->setPath('/')
            ->setHttpOnly($cookie->isHttpOnly())
            ->setSameSite('Lax')
            ->setDomain(parse_url(Context::$HOST_SCHEME . "://" . Context::$HOST_NAME, PHP_URL_HOST));

        \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Stdlib\CookieManagerInterface::class)->setPublicCookie(
            $cookie->getName(),
            $cookie->getValue(),
            $metadata
        );
        return true;
    }
}
