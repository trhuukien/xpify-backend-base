<?php
namespace SectionBuilder\Core\Model\Config\Source;

class PricingPlans implements \Magento\Framework\Option\ArrayInterface
{
    protected $pricingPlanRepository;

    protected $criteriaBuilder;

    protected $configData;

    public function __construct(
        \Xpify\PricingPlan\Model\PricingPlanRepository $pricingPlanRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \SectionBuilder\Core\Model\Config $configData
    ) {
        $this->pricingPlanRepository = $pricingPlanRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->configData = $configData;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $plans = $this->getPricingPlans();
        $result[] = [
            'label' => __(''),
            'value' => null
        ];
        foreach ($plans as $plan) {
            $result[] = [
                'label' => $plan->getData('name'),
                'value' => $plan->getData('entity_id')
            ];
        }
        return $result;
    }

    public function getPricingPlans()
    {
        $appId = $this->configData->getAppConnectingId(true);
        $criteria = $this->criteriaBuilder;
        $criteria->addFilter(
            \Xpify\PricingPlan\Api\Data\PricingPlanInterface::APP_ID,
            $appId
        );
        $searchResults = $this->pricingPlanRepository->getList($criteria->create());
        return $searchResults->getItems();
    }
}
