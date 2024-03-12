<?php
declare(strict_types=1);

namespace SectionBuilder\AssetGraphQl\Model\Resolver;

class UpdateAssetMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $validation;

    protected $authValidation;

    protected $serviceQuery;

    protected $handleUpdate;

    protected $sectionFactory;

    protected $sectionInstall;

    public function __construct(
        \SectionBuilder\Core\Model\GraphQl\Validation $validation,
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \Xpify\Asset\Model\UpdateAssetMutation $serviceQuery,
        \SectionBuilder\FileModifier\Model\Asset\HandleUpdateSections $handleUpdate,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall $sectionInstall
    ) {
        $this->validation = $validation;
        $this->authValidation = $authValidation;
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
        $merchant = $this->getMerchantSession()->getMerchant();

        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
            $args['asset']
        );
        $collection->joinListBought(
            [
                'b.merchant_shop IS NULL or b.merchant_shop = ?',
                $merchant->getShop()
            ]
        );
        $collection->getSelect()->joinLeft(
            ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
            'main_table.plan_id = IFNULL(xpp.entity_id, main_table.plan_id)',
            [
                'plan_code' => 'xpp.code',
                'plan_src_base' => 'xpp.section_builder_src_base',
            ]
        );
        $sectionItem = $collection->getFirstItem();
        $section = $sectionItem->getData();

        if ($section) {
            $hasPlan = $section['plan_code'] && $this->authValidation->hasPlan($merchant, $section['plan_code']);
            if ($section['plan_src_base']) {
                $collection = $this->sectionFactory->create();
                $collection->addFieldToFilter(
                    \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
                    $section['plan_src_base']
                );
                $assetBase = $collection->getFirstItem()->getData();
                $sourceBase = $assetBase['path_source'] ? $this->handleUpdate->getSource($assetBase['path_source']) : '';
            }

            $this->handleUpdate->changeArgs($section, $hasPlan, $sourceBase ?? '', $args);
            $result = $this->serviceQuery->resolve($merchant, $args);

            if (!isset($result['errors']) && isset($result['key'])) {
                $sectionItem->setData('qty_installed', ++$section['qty_installed']);
                $sectionItem->save();
                $this->replaceRowInstall($section, $merchant->getShop(), $args['theme_id']);

                if ($hasPlan && !empty($sourceBase) && !empty($assetBase)) {
                    /* Add file base to theme */
                    $resultBase = $this->serviceQuery->resolve($merchant, [
                        'theme_id' => $args['theme_id'],
                        'asset' => $assetBase['src'],
                        'value' => $sourceBase
                    ]);

                    if (!isset($resultBase['errors']) && isset($resultBase['key'])) {
                        $this->replaceRowInstall($assetBase, $merchant->getShop(), $args['theme_id']);
                    }
                }
            }
        }

        return $result;
    }

    public function replaceRowInstall($product, $shop, $themeId)
    {
        $this->sectionInstall->replaceRow(
            [
                'merchant_shop' => $shop,
                'product_id' => $product['entity_id'],
                'theme_id' => $themeId
            ],
            [
                'product_version' => $product['version']
            ]
        );
    }
}
