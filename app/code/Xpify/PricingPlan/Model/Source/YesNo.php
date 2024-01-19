<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class YesNo implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Yes')]
        ];
    }
}
