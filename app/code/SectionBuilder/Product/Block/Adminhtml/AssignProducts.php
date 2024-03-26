<?php
namespace SectionBuilder\Product\Block\Adminhtml;

class AssignProducts extends \Magento\Backend\Block\Template
{
    protected $_template = 'SectionBuilder_Product::products/assign_products.phtml';

    protected $blockGrid;

    protected $productFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $data);
    }

    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'SectionBuilder\Product\Block\Adminhtml\Tab\ProductGrid',
                'sb.list_product'
            );
        }
        return $this->blockGrid;
    }

    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    public function getProductsJson()
    {
        $currentProductId = $this->getRequest()->getParam('id');
        $typeId = $this->getRequest()->getParam('type_id');

        $dataPersistor = $this->dataPersistor->get('section_product_data');
        if (isset($dataPersistor['product_list'])) {
            $childIdsSelected = explode(",", $dataPersistor['product_list']);
        } else {
            if ($currentProductId) {
                $productFactory = $this->productFactory->create();
                $productFactory->addFieldToSelect(['child_ids', 'type_id']);
                $productFactory->addFieldToFilter('entity_id', ['eq' => $currentProductId]);
                $product = $productFactory->getFirstItem();
                $childIds = $product->getChildIds() ?? "";
                $childIdsSelected = explode(",", $childIds);
                $typeId = $product->getTypeId();
            }

            if ($typeId != \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID) {
                return '{}';
            }
        }

        $productFactory = $this->productFactory->create();
        $productFactory->addFieldToSelect(['entity_id']);
        $productFactory->addFieldToFilter('type_id', ['eq' => \SectionBuilder\Product\Model\Config\Source\ProductType::SIMPLE_TYPE_ID]);
        $productData = $productFactory->getData();

        $result = [];
        foreach ($productData as $product) {
            if (isset($childIdsSelected)) {
                if (in_array($product['entity_id'], $childIdsSelected)) {
                    $result[$product['entity_id']] = 1;
                } else {
                    $result[$product['entity_id']] = '';
                }
            } else {
                return '{}';
            }
        }

        return $result ? json_encode($result) : '{}';
    }

    public function getDataFormPart()
    {
        return 'section_builder_product_form';
    }
}
