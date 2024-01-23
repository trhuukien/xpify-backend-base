<?php
declare(strict_types=1);

namespace Xpify\ThemeGraphQl\Model\Resolver;

class ThemeQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    public const GRAPHQL_GET_THEME = 'getTheme';

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
        $onlyTheme = $field->getName() === self::GRAPHQL_GET_THEME;
        $this->validation->validateArgs(
            $args,
            $onlyTheme ? ['id'] : []
        );

        $apiVersion = \Shopify\Context::$API_VERSION;
        $themeId = isset($args['id']) ? '/' . $args['id'] : '';

        $response = $this->getMerchantSession()->getMerchant()->getRest()->get(
            '/admin/api/' . $apiVersion . '/themes' . $themeId . '.json'
        );

        return $response->getDecodedBody()['themes'] ?? $response->getDecodedBody()['theme'];
    }
}
