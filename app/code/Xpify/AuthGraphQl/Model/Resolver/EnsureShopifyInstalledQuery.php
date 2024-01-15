<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Shopify\Context;
use Shopify\Utils;
use Xpify\Auth\Service\AuthRedirection;
use Xpify\Merchant\Model\EnsureShopifyMerchantInstalled;

class EnsureShopifyInstalledQuery implements ResolverInterface
{
    private EnsureShopifyMerchantInstalled $ensureShopifyInstalled;
    private Uid $uidEncoder;
    private AuthRedirection $authRedirection;

    /**
     * @param EnsureShopifyMerchantInstalled $ensureShopifyInstalled
     * @param Uid $uidEncoder
     * @param AuthRedirection $authRedirection
     */
    public function __construct(
        EnsureShopifyMerchantInstalled $ensureShopifyInstalled,
        Uid $uidEncoder,
        AuthRedirection $authRedirection
    ) {
        $this->ensureShopifyInstalled = $ensureShopifyInstalled;
        $this->uidEncoder = $uidEncoder;
        $this->authRedirection = $authRedirection;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $app = $context->getExtensionAttributes()->getApp();
        $appId = $app?->getId();
        if (!$appId) {
            throw new \Exception("Something went wrong! Please contact us for support.");
        }
        $_query = ltrim($args['_query'], '?');
        parse_str($_query, $query);
        if (!isset($query['shop'])) {
            throw new \Exception("Something went wrong! Please contact us for support.");
        }

        $shop = Utils::sanitizeShopDomain($query['shop']);
        $installed = $this->ensureShopifyInstalled->execute((int) $appId, $shop);

        return [
            'installed' => $installed,
            'redirectQuery' => $installed ? null : $this->buildRedirectQuery((int) $appId, $query),
        ];
    }

    /**
     * Tạo chuỗi truy vấn URL chứa URI chuyển hướng
     *
     * @param int $appId ID của ứng dụng
     * @param array $query Các tham số truy vấn
     * @return string Chuỗi truy vấn URL chứa URI chuyển hướng
     */
    protected function buildRedirectQuery(int $appId, array $query): string
    {
        $redirectUri = $this->authRedirection->createAuthUrl($query['shop'], $appId);
        return http_build_query(array_merge($query, ["redirectUri" => $redirectUri]));
    }
}
