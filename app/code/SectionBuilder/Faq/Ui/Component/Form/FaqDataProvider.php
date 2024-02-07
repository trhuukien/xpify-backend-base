<?php
namespace SectionBuilder\Faq\Ui\Component\Form;

use SectionBuilder\Faq\Model\ResourceModel\Faq\CollectionFactory;

class FaqDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $faqCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $faqCollectionFactory->create();
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
        $data = $this->dataPersistor->get('section_faq_data');
        if (!empty($data)) {
            $this->loadedData[$data['entity_id'] ?? ""]['general'] = $data;
            $this->dataPersistor->clear('section_faq_data');
        } else {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->loadedData[$item->getId()]['general'] = $item->getData();
            }
        }

        return $this->loadedData;
    }
}
