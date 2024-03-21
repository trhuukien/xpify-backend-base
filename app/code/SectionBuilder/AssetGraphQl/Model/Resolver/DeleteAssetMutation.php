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

        if ($section) {
            $args['asset'] = $section['src'];
            $merchant = $this->getMerchantSession()->getMerchant();
            $result = $this->serviceQuery->resolve($merchant, $args);

            if (isset($result['message'])) {
                $this->sectionInstall->deleteRow(
                    [
                        'merchant_shop = ?' => $merchant->getShop(),
                        'product_id = ?' => $section['entity_id'],
                        'theme_id = ?' => $args['theme_id']
                    ]
                );
            }
        }

        return $result;
    }
}
