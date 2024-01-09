<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Block\Adminhtml\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class CancelButton implements ButtonProviderInterface
{
    /**
     * @var string
     */
    protected string $targetName = 'xpify_app_form.areas.pricing_plan.pricing_plan.xpify_pricingplan_update_modal';

    /**
     * Get Cancel button data
     *
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Cancel'),
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->targetName,
                                'actionName' => 'closeModal'
                            ],
                        ],
                    ],
                ],
            ],
            'sort_order' => 0
        ];
    }
}
