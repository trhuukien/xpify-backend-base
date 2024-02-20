<?php
namespace SectionBuilder\Product\Model\Config\Source;

class ProductType implements \Magento\Framework\Data\OptionSourceInterface
{
    const SIMPLE_TYPE_ID = 1;
    const GROUP_TYPE_ID = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SIMPLE_TYPE_ID, 'label' => __('Simple')],
            ['value' => self::GROUP_TYPE_ID, 'label' => __('Group')],
        ];
    }
}
