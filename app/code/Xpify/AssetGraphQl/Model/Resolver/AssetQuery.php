<?php
declare(strict_types=1);

namespace Xpify\AssetGraphQl\Model\Resolver;

class AssetQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    public const GRAPHQL_GET_ASSET = 'getAsset';

    /**
     * @var \Xpify\ThemeGraphQl\Model\Validation
     */
    protected $validation;

    /**
     * Construct.
     *
     * @param \Xpify\ThemeGraphQl\Model\Validation $validation
     */
    public function __construct(
        \Xpify\ThemeGraphQl\Model\Validation $validation
    ) {
        $this->validation = $validation;
    }

    /**
     * Execute.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param $context
     * @param \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|mixed
     * @throws \JsonException
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \Shopify\Exception\UninitializedContextException
     */
    public function execResolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $onlyFile = $field->getName() === self::GRAPHQL_GET_ASSET;
        $this->validation->validateArgs(
            $args,
            $onlyFile ? ['theme_id', 'asset'] : ['theme_id']
        );

        $apiVersion = \Shopify\Context::$API_VERSION;
        $themeId = $args['theme_id'];

        $response = $this->getMerchantSession()->getMerchant()->getRest()->get(
            '/admin/api/' . $apiVersion . '/themes/' . $themeId . '/assets.json',
            [],
            $onlyFile ? ['asset[key]' => $args['asset']] : []
        );

        if (isset($args['asset'])) {
            return $response->getDecodedBody()['asset'] ?? [];
        }
        return $response->getDecodedBody()['assets'] ?? [];
    }

    /**
     * Validate input arguments
     *
     * @param array $args
     * @return void
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     */
    public function validateArgs($args)
    {
        if (!isset($args['theme_id']) || $args['theme_id'] == '') {
            throw new \Magento\Framework\GraphQl\Exception\GraphQlInputException(
                __("Invalid theme_id")
            );
        }

        if (isset($args['asset']) && $args['asset'] == '') {
            throw new \Magento\Framework\GraphQl\Exception\GraphQlInputException(
                __("Invalid asset")
            );
        }
    }
}
