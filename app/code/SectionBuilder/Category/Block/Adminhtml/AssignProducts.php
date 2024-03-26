<?php
namespace SectionBuilder\Category\Block\Adminhtml;

class AssignProducts extends \SectionBuilder\Product\Block\Adminhtml\AssignProducts
{
    protected $categoryProductCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productFactory,
        \SectionBuilder\Category\Model\ResourceModel\CategoryProduct\CollectionFactory $categoryProductCollectionFactory,
        array $data = []
    ) {
        $this->categoryProductCollectionFactory = $categoryProductCollectionFactory;
        parent::__construct($context, $productFactory, $data);
    }

    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'SectionBuilder\Category\Block\Adminhtml\Tab\ProductGrid',
                'sb.products'
            );
        }
        return $this->blockGrid;
    }

    public function getProductsJson()
    {
        $result = '{}';
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return $result;
        }
        $collection = $this->categoryProductCollectionFactory->create()
            ->addFieldToFilter('category_id', $id)
            ->addFieldToSelect('product_id');
        $productData = $collection->getData();
        $productIds = $productData ? array_column($productData, 'product_id') : [];

        $result = [];
        foreach ($productData as $product) {
            if (in_array($product['product_id'], $productIds)) {
                $result[$product['product_id']] = 1;
            } else {
                $result[$product['product_id']] = '';
            }
        }

        return $result ? json_encode($result) : '{}';
    }

    public function getDataFormPart()
    {
        return 'section_builder_category_form';
    }
}
