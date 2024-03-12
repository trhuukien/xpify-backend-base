<?php
namespace SectionBuilder\Core\Model\Config\Source;

class PricingPlans implements \Magento\Framework\Option\ArrayInterface
{
    protected $data;
    public function __construct(
        \SectionBuilder\Core\Model\Data $data
    ) {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $plans = $this->data->getPricingPlans();
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
}
