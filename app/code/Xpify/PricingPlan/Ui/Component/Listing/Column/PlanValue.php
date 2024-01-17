<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Directory\Model\Currency;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;
use Xpify\PricingPlan\Model\Source\IntervalType;

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
                $item[$this->getData('name')] = $this->prepareItemPrice($item[$this->getData('name')]);
            }
        }

        return $dataSource;
    }

    /**
     * Prepare item price
     *
     * @param string $rawData
     * @return array
     */
    protected function prepareItemPrice(string $rawData): array
    {
        $decodedData = json_decode($rawData, true);
        foreach ($decodedData as &$value) {
            $value['formatted_amount'] = $this->currency->format($value['amount'], [], false);
            $value['interval'] = IntervalType::getIntervalLabel($value['interval']);
        }
        return $decodedData;
    }
}
