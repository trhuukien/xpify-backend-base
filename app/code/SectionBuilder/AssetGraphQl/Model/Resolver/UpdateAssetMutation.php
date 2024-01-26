<?php
declare(strict_types=1);

namespace SectionBuilder\AssetGraphQl\Model\Resolver;

use SectionBuilder\FileModifier\Model\Asset\HandleUpdate;

class UpdateAssetMutation extends \Xpify\Asset\Model\UpdateAssetMutation implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \SectionBuilder\Product\Model\ResourceModel\SectionInstall\CollectionFactory
     */
    protected $sectionInstallFactory;

    public function __construct(
        \Xpify\Theme\Model\Validation $validation,
        \SectionBuilder\FileModifier\Model\Asset\HandleUpdate $handleUpdate,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall\CollectionFactory $sectionInstallFactory
    ) {
        parent::__construct($validation);
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
        $this->handleUpdate->beforeUpdateAssetGraphql($args, $merchant);

        return parent::execResolve($field, $context, $info, $value, $args);
    }
}
