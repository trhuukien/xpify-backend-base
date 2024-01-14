<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Shopify\Context;
use Shopify\Utils;
use Xpify\Merchant\Model\EnsureShopifyMerchantInstalled;

class EnsureShopifyInstalledQuery implements ResolverInterface
{
    private EnsureShopifyMerchantInstalled $ensureShopifyInstalled;
    private Uid $uidEncoder;

    /**
     * @param EnsureShopifyMerchantInstalled $ensureShopifyInstalled
     * @param Uid $uidEncoder
     */
    public function __construct(EnsureShopifyMerchantInstalled $ensureShopifyInstalled, Uid $uidEncoder)
    {
        $this->ensureShopifyInstalled = $ensureShopifyInstalled;
        $this->uidEncoder = $uidEncoder;
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
     * Phương thức này được sử dụng để xây dựng URI chuyển hướng cho ứng dụng Shopify.
     * Đầu tiên, nó tạo một chữ ký HMAC bằng cách sử dụng ID ứng dụng và các tham số truy vấn.
     * Chữ ký HMAC được tạo bằng cách sử dụng khóa bí mật hệ thống.
     * Sau đó, nó lấy tên host của ứng dụng từ ngữ cảnh.
     * URI chuyển hướng được xây dựng bằng cách sử dụng tên host, ID ứng dụng, tên miền cửa hàng từ các tham số truy vấn, và chữ ký HMAC.
     * URI chuyển hướng sau đó được mã hóa URL.
     * Cuối cùng, URI chuyển hướng được thêm vào các tham số truy vấn và trả về dưới dạng chuỗi truy vấn URL.
     *
     * @param int $appId ID của ứng dụng
     * @param array $query Các tham số truy vấn
     * @return string Chuỗi truy vấn URL chứa URI chuyển hướng
     */
    protected function buildRedirectQuery(int $appId, array $query): string
    {
        $sign = \Xpify\Core\Helper\Utils::createHmac([
            'data' => ['shop' => $query['shop'], '_i' => $appId],
            'buildQuery' => true,
            'buildQueryWithJoin' => true,
        ], \Xpify\Core\Model\Constants::SYS_SECRET_KEY);
        $appHost = Context::$HOST_NAME;
        $redirectUri = urlencode("https://$appHost/api/auth?shop={$query['shop']}&_i={$this->uidEncoder->encode((string) $appId)}&sign=$sign");
        return http_build_query(array_merge($query, ["redirectUri" => $redirectUri]));
    }
}
