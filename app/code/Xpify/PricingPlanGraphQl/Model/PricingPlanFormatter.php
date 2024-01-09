<?php
declare(strict_types=1);

namespace Xpify\PricingPlanGraphQl\Model;

use Magento\Framework\GraphQl\Query\Uid;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

class PricingPlanFormatter
{
    private Uid $uidEncoder;

    /**
     * @param Uid $uidEncoder
     */
    public function __construct(Uid $uidEncoder)
    {
        $this->uidEncoder = $uidEncoder;
    }

    /**
     * Convert Pricing Plan to GraphQl output
     *
     * @param IPricingPlan $pricingPlan
     * @return array
     */
    public function toGraphQlOutput(IPricingPlan $pricingPlan): array
    {
        return [
            'model' => $pricingPlan,
            'id' => $this->uidEncoder->encode((string) $pricingPlan->getId()),
            'currency' => IPricingPlan::BASE_CURRENCY,
            IPricingPlan::STATUS => (bool) $pricingPlan->getStatus(),
            IPricingPlan::NAME => $pricingPlan->getName(),
            IPricingPlan::PRICE => $pricingPlan->getPrice(),
            IPricingPlan::DESCRIPTION => $pricingPlan->getDescription(),
            IPricingPlan::ENABLE_FREE_TRIAL => $pricingPlan->isEnableFreeTrial(),
            IPricingPlan::FREE_TRIAL_DAYS => $pricingPlan->getFreeTrialDays(),
            IPricingPlan::SORT_ORDER => $pricingPlan->getSortOrder(),
        ];
    }
}
