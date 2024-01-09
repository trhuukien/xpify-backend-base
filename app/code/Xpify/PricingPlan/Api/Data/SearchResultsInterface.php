<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Api\Data;

use Magento\Framework\Api\SearchResultsInterface as BaseSearchResultsInterface;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

interface SearchResultsInterface extends BaseSearchResultsInterface
{
    /**
     * Get plan list.
     *
     * @return IPricingPlan[]
     */
    public function getItems();

    /**
     * Set plan list.
     *
     * @param IPricingPlan[] $items
     * @return $this
     */
    public function setItems(array $items);
}
