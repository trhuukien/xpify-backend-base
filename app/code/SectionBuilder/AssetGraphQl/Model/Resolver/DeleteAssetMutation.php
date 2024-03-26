<?php
declare(strict_types=1);

namespace SectionBuilder\AssetGraphQl\Model\Resolver;

class DeleteAssetMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $validation;

    protected $serviceQuery;

    protected $sectionFactory;

    protected $sectionInstall;

    public function __construct(
        \SectionBuilder\Core\Model\GraphQl\Validation $validation,
        \Xpify\Asset\Model\DeleteAssetMutation $serviceQuery,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory,
        \SectionBuilder\Product\Model\ResourceModel\SectionInstall $sectionInstall
    ) {
        $this->validation = $validation;
        $this->serviceQuery = $serviceQuery;
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

        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            \SectionBuilder\Product\Api\Data\SectionInterface::KEY,
            $args['key']
        );
        $sectionItem = $collection->getFirstItem();
        $section = $sectionItem->getData();

        if (!$section) {
            return $result;
        }
        $merchant = $this->getMerchantSession()->getMerchant();
        if ($section['type_id'] == \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID) {
            $childIds = explode(",", $section['child_ids']);
            foreach ($childIds as $id) {
                $collection = $this->sectionFactory->create();
                $collection->addFieldToFilter(
                    'main_table.entity_id',
                    $id
                );
                $collection->addFieldToSelect(['entity_id', 'src']);
                $sectionChildItem = $collection->getFirstItem();
                $sectionChild = $sectionChildItem->getData();

                if ($sectionChild) {
                    $this->execute($merchant, $sectionChild, $args);
                }
            }
            $this->deleteRowInstall($section, $merchant->getShop(), $args['theme_id']);
        } else {
            $this->execute($merchant, $section, $args);
        }

        return $result;
    }

    public function execute($merchant, $section, $args)
    {
        $args['asset'] = $section['src'];
        $result = $this->serviceQuery->resolve($merchant, $args);
        $this->deleteRowInstall($section, $merchant->getShop(), $args['theme_id']);
    }

    public function deleteRowInstall($section, $shop, $themeId)
    {
        $this->sectionInstall->deleteRow(
            [
                'merchant_shop = ?' => $shop,
                'product_id = ?' => $section['entity_id'],
                'theme_id = ?' => $themeId
            ]
        );
    }
}
