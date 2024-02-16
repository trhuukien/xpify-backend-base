<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

class SectionQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \SectionBuilder\Core\Model\Auth\Validation
     */
    protected $authValidation;

    /**
     * @var \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory
     */
    protected $sectionFactory;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $sectionFactory
    ) {
        $this->authValidation = $authValidation;
        $this->sectionFactory = $sectionFactory;
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
        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            'main_table.url_key',
            $args['key']
        );
//        $collection->join(
//            ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
//            'main_table.' . \SectionBuilder\Product\Api\Data\SectionInterface::PLAN_ID . ' = xpp.' .
//            \SectionBuilder\Product\Api\Data\SectionInterface::ID,
//            "xpp." . \Xpify\PricingPlan\Api\Data\PricingPlanInterface::NAME . " as plan_need_subscribe"
//        );
        $result = $collection->getFirstItem()->getData();

        if (!$result) {
            return $result;
        }

        if (1) { // Free
            $result['is_show_install'] = true;
            $result['is_show_purchase'] = false;
            $result['is_show_plan'] = false;
        } else {
            $merchant = $this->getMerchantSession()->getMerchant();
            $bought = $this->authValidation->hasOneTime(
                $merchant,
                $result[\SectionBuilder\Product\Api\Data\SectionInterface::KEY]
            );
            $result['is_show_plan'] = !$this->authValidation->hasPlan($merchant, $result['plan_need_subscribe']);
            $result['is_show_install'] = !$result['is_show_plan'] || $bought;
            $result['is_show_purchase'] = !$bought;
        }

        return $result;
    }
}
