<?php
declare(strict_types=1);

namespace Xpify\Theme\Model;

class CreateThemeMutation
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

        $response = $merchant->getRest()->post(
            "/admin/api/$apiVersion/themes.json",
            null,
            [],
            [
                'theme' => [
                    'name' => $args['name'],
                    'src' => $args['src'],
                    'role' => $args['role']
                ]
            ]
        );

        return $response->getDecodedBody()['theme'] ?? $response->getDecodedBody();
    }
}
