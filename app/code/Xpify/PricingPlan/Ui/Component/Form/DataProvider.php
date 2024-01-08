<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface;
use Xpify\PricingPlan\Model\ResourceModel\PricingPlan\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param ContextInterface $context
     * @param array $meta
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ContextInterface $context,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->context = $context;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get roles data
     *
     * @return array
     */
    public function getData(): array
    {
        if (null !== $this->loadedData) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var PricingPlanInterface $item */
        foreach ($items as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }
        $this->loadedData[''] = $this->getDefaultData();

        return $this->loadedData;
    }

    /**
     * Get default customer data for adding new role
     *
     * @return array
     */
    private function getDefaultData(): array
    {
        return [
            'app_id' => $this->context->getRequestParam('app_id'),
        ];
    }
}
