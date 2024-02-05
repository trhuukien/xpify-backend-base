<?php
declare(strict_types=1);

namespace SectionBuilder\AssetGraphQl\Model\Resolver;

class UpdateAssetMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $validation;

    protected $serviceQuery;

    protected $handleUpdate;

    protected $sectionInstallFactory;

    public function __construct(
        \SectionBuilder\Core\Model\GraphQl\Validation $validation,
        \Xpify\Asset\Model\UpdateAssetMutation $serviceQuery,
        \SectionBuilder\FileModifier\Model\Asset\HandleUpdate $handleUpdate,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall\CollectionFactory $sectionInstallFactory
    ) {
        $this->validation = $validation;
        $this->serviceQuery = $serviceQuery;
        $this->handleUpdate = $handleUpdate;
        $this->sectionInstallFactory = $sectionInstallFactory;
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
            ['theme_id', 'asset'],
            ['value']
        );

        $merchant = $this->getMerchantSession()->getMerchant();
        $this->handleUpdate->beforeUpdateAssetGraphql($merchant, $args);
        return $this->serviceQuery->resolve($merchant, $args);
    }
}
