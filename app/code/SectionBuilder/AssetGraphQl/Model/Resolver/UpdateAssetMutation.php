<?php
declare(strict_types=1);

namespace SectionBuilder\AssetGraphQl\Model\Resolver;

class UpdateAssetMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $validation;

    protected $serviceQuery;

    protected $handleUpdate;

    protected $sectionFactory;

    protected $sectionInstall;

    public function __construct(
        \SectionBuilder\Core\Model\GraphQl\Validation $validation,
        \Xpify\Asset\Model\UpdateAssetMutation $serviceQuery,
        \SectionBuilder\FileModifier\Model\Asset\HandleUpdateSections $handleUpdate,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall $sectionInstall
    ) {
        $this->validation = $validation;
        $this->serviceQuery = $serviceQuery;
        $this->handleUpdate = $handleUpdate;
        $this->sectionFactory = $sectionFactory;
        $this->sectionInstall = $sectionInstall;
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
        $result = [];
        $this->validation->validateArgs(
            $args,
            ['theme_id', 'asset'],
            ['value']
        );

        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
            $args['asset']
        );
        $collection->getSelect()->joinLeft(
            ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
            'main_table.plan_id = IFNULL(xpp.entity_id, main_table.plan_id)',
            "xpp.code as plan_code"
        );
        $sectionItem = $collection->getFirstItem();
        $section = $sectionItem->getData();

        if ($section) {
            $merchant = $this->getMerchantSession()->getMerchant();
            $this->handleUpdate->beforeUpdateAssetGraphql($section, $merchant, $args);
            $result = $this->serviceQuery->resolve($merchant, $args);

            $this->sectionInstall->addRowUniqueKey(
                [
                    'merchant_shop' => $merchant->getShop(),
                    'product_id' => $section['entity_id'],
                    'theme_id' => $args['theme_id']
                ],
                [
                    'product_version' => $section['version']
                ]
            );

            if (!isset($result['errors'])) {
                $section->setData('qty_installed', $section->getData('qty_installed') + 1);
                $section->save();
            }
        }

        return $result;
    }
}
