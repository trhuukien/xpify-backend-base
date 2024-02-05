<?php
declare(strict_types=1);

namespace SectionBuilder\FileModifier\Model\Asset;

class HandleUpdate
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

    public function beforeUpdateAssetGraphql(
        \Xpify\Merchant\Api\Data\MerchantInterface $merchant,
        array &$args
    ): void {
        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
            $args['asset']
        );
        $collection->join(
            ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
            'main_table.plan_id = xpp.entity_id',
            "xpp.name as plan_need_subscribe"
        );
        $section = $collection->getFirstItem()->getData();

        if ($section) {
            $hasOneTime = $this->authValidation->hasOneTime($merchant, $section['name']);
            $hasPlan = $this->authValidation->hasPlan($merchant, $section['plan_need_subscribe']);

            if (!str_contains($args['asset'], ".liquid")
                && ($hasOneTime || $hasPlan)
            ) {
                $args['value'] = $section[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA];
                return;
            }

            if ($hasOneTime) {
                $collection = $this->sectionFactory->create();
                $collection->addFieldToFilter(
                    \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
                    \SectionBuilder\Product\Model\ResourceModel\Section::FILE_BASE_CSS
                );
                $baseCssData = $collection->getFirstItem()->getData();

                if (isset($baseCssData[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA])) {
                    $args['value'] = "{% style %}\n" .
                        $baseCssData[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA] .
                        "{% endstyle %}\n\n" .
                        $section[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA];
                }
            } else {
                if ($hasPlan) {
                    $args['value'] = $section[\SectionBuilder\Product\Api\Data\SectionInterface::FILE_DATA];
                } else {
                    throw new \Magento\Framework\Exception\AuthorizationException(__("Nang cap ban pro hoac mua section nay"));
                }
            }
        }
    }
}
