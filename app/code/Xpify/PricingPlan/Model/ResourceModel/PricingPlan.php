<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

class PricingPlan extends AbstractDb
{
    const MAIN_TABLE = 'xpify_pricing_plans';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, IPricingPlan::ID);
    }
}
