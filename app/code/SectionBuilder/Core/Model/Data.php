<?php
namespace SectionBuilder\Core\Model;

class Data
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

    public function getPricingPlans()
    {
        $appId = $this->configData->getAppConnectingId(true);
        $criteria = $this->criteriaBuilder;
        $criteria->addFilter(
            \Xpify\PricingPlan\Api\Data\PricingPlanInterface::APP_ID,
            $appId
        );
        $criteria->addFilter(
            \Xpify\PricingPlan\Api\Data\PricingPlanInterface::STATUS,
            1
        );
        $searchResults = $this->pricingPlanRepository->getList($criteria->create());

        return $searchResults->getItems();
    }
}
