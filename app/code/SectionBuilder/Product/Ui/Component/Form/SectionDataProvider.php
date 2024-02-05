<?php
namespace SectionBuilder\Product\Ui\Component\Form;

use SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory;

class SectionDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const EXPLODE_FIELDS = ['categories', 'tags'];

    protected $dataPersistor;


    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sectionCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $sectionCollectionFactory->create()->joinListCategoryId()->joinListTagId()->groupById();
        $this->dataPersistor = $dataPersistor;
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

        $this->loadedData = [];
        $data = $this->dataPersistor->get('section_product_data');
        if (!empty($data)) {
            $this->loadedData[$data['entity_id'] ?? ""] = $data;
            $this->dataPersistor->clear('section_product_data');
        } else {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                foreach (self::EXPLODE_FIELDS as $field) {
                    $data = $item->getData($field);
                    if ($data !== null) {
                        $item->setData($field, explode(',', $data));
                    }
                }

                $this->loadedData[$item->getId()] = $item->getData();
            }
        }

        return $this->loadedData;
    }
}
