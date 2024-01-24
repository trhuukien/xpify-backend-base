<?php
declare(strict_types=1);

namespace Xpify\Theme\Model;

class CreateThemeMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \Xpify\Theme\Model\Validation
     */
    protected $validation;

    /**
     * Construct.
     *
     * @param \Xpify\Theme\Model\Validation $validation
     */
    public function __construct(
        \Xpify\Theme\Model\Validation $validation
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
        $this->validation->validateArgs($args, ['name', 'src', 'role']);

        $apiVersion = \Shopify\Context::$API_VERSION;

        $response = $this->getMerchantSession()->getMerchant()->getRest()->post(
            '/admin/api/' . $apiVersion . '/themes.json',
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

        return $response->getDecodedBody()['theme'] ?? [];
    }
}
