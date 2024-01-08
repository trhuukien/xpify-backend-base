<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Model\ResourceModel\PricingPlan;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

class Collection extends AbstractCollection
{
    protected $_idFieldName = IPricingPlan::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            \Xpify\PricingPlan\Model\PricingPlan::class,
            \Xpify\PricingPlan\Model\ResourceModel\PricingPlan::class
        );
    }
}
