<?php
declare(strict_types=1);

namespace SectionBuilder\CoreGraphQl\Model\Resolver;

class PricingPlansQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $collectionFactory;

    protected $configData;

    public function __construct(
        \Xpify\PricingPlan\Model\ResourceModel\PricingPlan\CollectionFactory $collectionFactory,
        \SectionBuilder\Core\Model\Config $configData
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->configData = $configData;
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
        $appId = $this->configData->getAppConnectingId();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('app_id', $appId);
        $collection->addFieldToFilter('status', 1);
        return array_merge(
            [['entity_id' => 0, 'name' => 'Free']],
            $collection->getData()
        );
    }
}
