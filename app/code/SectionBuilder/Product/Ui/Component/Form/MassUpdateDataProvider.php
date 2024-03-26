<?php
namespace SectionBuilder\Product\Ui\Component\Form;

use SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory;

class MassUpdateDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public $productCollection;

    protected $loadedData;

    protected $dataProvider;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sectionCollectionFactory,
        \SectionBuilder\Product\Model\DataProvider $dataProvider,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $sectionCollectionFactory->create()->groupById();
        $this->dataProvider = $dataProvider;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData['']['general']['product_ids'] = $this->dataProvider->getProductIdsUpdating();

        return $this->loadedData;
    }
}
