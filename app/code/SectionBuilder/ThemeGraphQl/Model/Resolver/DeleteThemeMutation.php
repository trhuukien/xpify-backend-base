<?php
declare(strict_types=1);

namespace SectionBuilder\ThemeGraphQl\Model\Resolver;

class DeleteThemeMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $validation;

    protected $serviceQuery;

    public function __construct(
        \SectionBuilder\Core\Model\GraphQl\Validation $validation,
        \Xpify\Theme\Model\DeleteThemeMutation $serviceQuery
    ) {
        $this->validation = $validation;
        $this->serviceQuery = $serviceQuery;
    }

    /**
     * @inheirtdoc
     */
    public function execResolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->validation->validateArgs(
            $args,
            ['id']
        );
        $merchant = $this->getMerchantSession()->getMerchant();
        return $this->serviceQuery->resolve($merchant, $args);
    }
}
