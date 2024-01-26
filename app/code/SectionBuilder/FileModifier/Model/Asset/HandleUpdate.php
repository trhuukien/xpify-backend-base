<?php
declare(strict_types=1);

namespace SectionBuilder\FileModifier\Model\Asset;

class HandleUpdate {
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

    public function beforeUpdateAssetGraphql(&$args, $merchant): void
    {
        $colCheckPlan = 'plan_need_subscribe';
        $collection = $this->sectionFactory->create();
        $collection->addFieldToFilter(
            \SectionBuilder\Product\Api\Data\SectionInterface::SRC,
            $args['asset']
        );
        $collection->join(
            ['xpp' => \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::MAIN_TABLE],
            'main_table.' . \SectionBuilder\Product\Api\Data\SectionInterface::PLAN_IDS .
            ' = xpp.' . \Xpify\PricingPlan\Api\Data\PricingPlanInterface::ID,
            "xpp." . \Xpify\PricingPlan\Api\Data\PricingPlanInterface::NAME . " as $colCheckPlan"
        );
        $section = $collection->getFirstItem()->getData();

        if ($section) {
            $hasOneTime = $this->authValidation->hasOneTime($merchant, $section['name']);
            $hasPlan = $this->authValidation->hasPlan($merchant, $section[$colCheckPlan]);

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

                if (isset($baseCssData['file_data'])) {
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
