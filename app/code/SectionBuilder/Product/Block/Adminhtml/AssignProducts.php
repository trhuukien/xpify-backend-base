<?php
namespace SectionBuilder\Product\Block\Adminhtml;

class AssignProducts extends \Magento\Backend\Block\Template
{
    protected $_template = 'products/assign_products.phtml';

    protected $blockGrid;

    protected $registry;

    protected $jsonEncoder;

    protected $productFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'SectionBuilder\Product\Block\Adminhtml\Tab\Productgrid',
                'sb.products'
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
        $typeId = $this->getRequest()->getParam('type_id');
        $currentProductId = $this->getRequest()->getParam('id');
        if ($currentProductId) {
            $productFactory = $this->productFactory->create();
            $productFactory->addFieldToSelect(['child_ids', 'type_id']);
            $productFactory->addFieldToFilter('entity_id', ['eq' => $currentProductId]);
            $product = $productFactory->getFirstItem();
            $childIdsSelected = explode(",", $product->getChildIds() ?? "");
            $typeId = $product->getTypeId();
        }
        if ($typeId != \SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID) {
            return '{}';
        }

        $productFactory = $this->productFactory->create();
        $productFactory->addFieldToSelect(['entity_id']);
        $productFactory->addFieldToFilter('type_id', ['eq' => 1]);
        $productData = $productFactory->getData();

        $result = [];
        foreach ($productData as $product) {
            if (isset($childIdsSelected) && in_array($product['entity_id'], $childIdsSelected)) {
                $result[$product['entity_id']] = 1;
            } else {
                $result[$product['entity_id']] = '';
            }
        }

        return $result ? $this->jsonEncoder->encode($result) : '{}';
    }
}
