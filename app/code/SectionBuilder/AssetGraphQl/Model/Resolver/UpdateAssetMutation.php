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

    protected $planList = [];

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
            ['theme_id', 'key']
        );
        $merchant = $this->getMerchantSession()->getMerchant();

        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            \SectionBuilder\Product\Api\Data\SectionInterface::KEY,
            $args['key']
        );
        $collection->joinListBought('AND b.merchant_shop = "' . $merchant->getShop() . '"');
        $collection->addFieldToSelect(['name', 'price', 'src', 'path_source', 'type_id', 'child_ids', 'version', 'qty_installed']);
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

        if (!$section) {
            return $result;
        }

        if ($section['type_id'] == \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID) {
            $isOwned = $section['bought_id'] || ($section['plan_code'] === null && $section['price'] == 0);
            if ($isOwned) {
                $childIds = explode(",", $section['child_ids']);
                foreach ($childIds as $id) {
                    $collection = $this->sectionFactory->create();
                    $collection->addFieldToFilter(
                        'main_table.entity_id',
                        $id
                    );
                    $collection->addFieldToSelect(['name', 'price', 'src', 'path_source', 'version', 'qty_installed']);
                    $collection->getSelect()->joinLeft(
                        ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
                        'main_table.plan_id = IFNULL(xpp.entity_id, main_table.plan_id)',
                        ['plan_src_base' => 'xpp.section_builder_src_base']
                    );
                    $sectionChildItem = $collection->getFirstItem();
                    $sectionChild = $sectionChildItem->getData();

                    if ($sectionChild) {
                        // Auto owned child product
                        $sectionChild['bought_id'] = 1;
                        $sectionChild['plan_code'] = null;
                        $result[] = $this->execute($merchant, $sectionChild, $sectionChildItem, $args);
                    }
                }

                if ($result) {
                    $this->updateQtyInstall($section, $sectionItem, $merchant->getShop(), $args['theme_id']);
                }
            }
        } else {
            $result[] = $this->execute($merchant, $section, $sectionItem, $args);
        }

        return $result;
    }

    public function execute($merchant, $section, $sectionItem, $args)
    {
        $args['asset'] = $section['src'];

        $hasPlan = $section['plan_code'] && $this->authValidation->hasPlan($merchant, $section['plan_code']);
        if (isset($section['plan_src_base'])) {
            if (isset($this->planList[$section['plan_src_base']])) {
                list($assetBase, $sourceBase) = $this->planList[$section['plan_src_base']];
            } else {
                $collection = $this->sectionFactory->create();
                $collection->addFieldToFilter(
                    \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
                    $section['plan_src_base']
                );
                $collection->addFieldToSelect(['src', 'path_source']);
                $assetBase = $this->planList[$section['plan_src_base']][] = $collection->getFirstItem()->getData();
                $sourceBase = $this->planList[$section['plan_src_base']][]
                    = $assetBase['path_source'] ? $this->handleUpdate->getSource($assetBase['path_source']) : '';
            }
        }

        $this->handleUpdate->changeArgs($section, $hasPlan, $sourceBase ?? '', $args);
        $result = $this->serviceQuery->resolve($merchant, $args);

        if (!isset($result['errors']) && isset($result['key'])) {
            $this->updateQtyInstall($section, $sectionItem, $merchant->getShop(), $args['theme_id']);

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

        $result['name'] = $section['name'];
        return $result;
    }

    public function updateQtyInstall($section, $sectionItem, $shop, $themeId)
    {
        $sectionItem->setData('qty_installed', ++$section['qty_installed']);
        $sectionItem->save();
        $this->replaceRowInstall($section, $shop, $themeId);
    }

    public function replaceRowInstall($section, $shop, $themeId)
    {
        $this->sectionInstall->replaceRow(
            [
                'merchant_shop' => $shop,
                'product_id' => $section['entity_id'],
                'theme_id' => $themeId
            ],
            [
                'product_version' => $section['version']
            ]
        );
    }
}
