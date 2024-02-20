<?php
namespace SectionBuilder\Product\Ui\Component\Form;

use SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory;

class SectionDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const EXPLODE_FIELDS = ['categories', 'tags'];

    protected $dataPersistor;

    protected $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sectionCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\Request\Http $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $sectionCollectionFactory->create()->joinListCategoryId()->joinListTagId()->groupById();
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
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


        $this->loadedData[$data['entity_id'] ?? ""]['is_group_product']
            = $isGroup
            = $this->request->getParam('type_id') == \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID;

        if (!empty($data)) {
            if (isset($data['entity_id'])) {
                $this->loadedData[$data['entity_id']] = $data;
            } else {
                $this->loadedData[""] = $data;
                $this->loadedData[""]['is_disable'] = false;
            }

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
                $this->loadedData[$item->getId()]['is_disable'] = true;
                $this->loadedData[$item->getId()]['is_group_product']
                    = $isGroup || $item->getData('type_id') == \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID;
            }
        }

        return $this->loadedData;
    }
}
