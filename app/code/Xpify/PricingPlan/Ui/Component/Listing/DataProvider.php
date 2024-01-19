<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Ui\Component\Listing;

use Xpify\PricingPlan\Api\Data\PricingPlanInterface;
use Xpify\PricingPlan\Model\ResourceModel\PricingPlan\Grid\CollectionFactory as FPricingPlanGridCollection;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @param FPricingPlanGridCollection $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        FPricingPlanGridCollection $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get pricing plans data by app
     *
     * @return array
     */
    public function getData()
    {
        $collection = $this->getCollection();
        $data['items'] = [];
        $appId = $this->request->getParam('app_id');
        if (!$appId) {
            return $data;
        }
        $collection->addFieldToFilter(PricingPlanInterface::APP_ID, $appId);
        return $collection->toArray();
    }
}
