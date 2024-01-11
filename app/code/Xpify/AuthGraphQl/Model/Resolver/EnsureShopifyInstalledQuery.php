<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Shopify\Utils;
use Xpify\Merchant\Model\EnsureShopifyMerchantInstalled;

class EnsureShopifyInstalledQuery implements ResolverInterface
{
    private EnsureShopifyMerchantInstalled $ensureShopifyInstalled;

    /**
     * @param EnsureShopifyMerchantInstalled $ensureShopifyInstalled
     */
    public function __construct(EnsureShopifyMerchantInstalled $ensureShopifyInstalled)
    {
        $this->ensureShopifyInstalled = $ensureShopifyInstalled;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $appId = $context->getExtensionAttributes()->getAppId();
        if (!$appId) {
            throw new \Exception("Something went wrong! Please contact us for support.");
        }
        $_query = $args['_query'];
        parse_str($_query, $query);
        if (!isset($query['shop'])) {
            throw new \Exception("Something went wrong! Please contact us for support.");
        }

        $shop = Utils::sanitizeShopDomain($query['shop']);
        $installed = $this->ensureShopifyInstalled->execute((int) $appId, $shop);

        return [
            'installed' => $installed,
            'redirectUri' => $this->buildRedirectUri((int) $appId, $query)
        ];
    }

    protected function buildRedirectUri(int $appId, array $query): string
    {

    }
}
