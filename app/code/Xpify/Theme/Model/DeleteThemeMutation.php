<?php
declare(strict_types=1);

namespace Xpify\Theme\Model;

class DeleteThemeMutation
{
    /**
     * Execute.
     *
     * @param \Xpify\Merchant\Api\Data\MerchantInterface $merchant
     * @param array $args
     * @return array|mixed|string|null
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \Shopify\Exception\UninitializedContextException
     */
    public function resolve(
        \Xpify\Merchant\Api\Data\MerchantInterface $merchant,
        array $args
    ) {
        $apiVersion = \Shopify\Context::$API_VERSION;
        $id = $args['id'];

        $response = $merchant->getRest()->delete(
            "/admin/api/$apiVersion/themes/$id.json"
        );

        return $response->getDecodedBody();
    }
}
