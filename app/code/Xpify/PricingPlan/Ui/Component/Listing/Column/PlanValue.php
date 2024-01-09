<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Directory\Model\Currency;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

class PlanValue extends Column
{
    private Currency $currency;

    /**
     * @param Currency $currency
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Currency $currency,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->currency = $currency;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $purchaseCurrency = $this->currency->load(IPricingPlan::BASE_CURRENCY);
                $item[$this->getData('name')] = $purchaseCurrency
                    ->format($item[$this->getData('name')], [], false);
            }
        }

        return $dataSource;
    }
}
