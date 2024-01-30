<?php
namespace SectionBuilder\Tag\Ui\Component\Form;

use SectionBuilder\Tag\Model\ResourceModel\Tag\CollectionFactory;

class TagDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $tagCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $tagCollectionFactory->create();
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
        $data = $this->dataPersistor->get('section_tag_data');
        if (!empty($data)) {
            $this->loadedData[$data['entity_id'] ?? ""]['general'] = $data;
            $this->dataPersistor->clear('section_tag_data');
        } else {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->loadedData[$item->getId()]['general'] = $item->getData();
            }
        }

        return $this->loadedData;
    }
}
