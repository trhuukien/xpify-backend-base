<?php
namespace SectionBuilder\Category\Ui\Component\Form;

use SectionBuilder\Category\Model\ResourceModel\Category\CollectionFactory;

class CategoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $categoryCollectionFactory->create();
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
        $data = $this->dataPersistor->get('section_category_data');
        if (!empty($data)) {
            $this->loadedData[$data['entity_id'] ?? ""]['general'] = $data;
            $this->dataPersistor->clear('section_category_data');
        } else {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->loadedData[$item->getId()]['general'] = $item->getData();
            }
        }

        return $this->loadedData;
    }
}
