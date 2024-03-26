<?php
namespace SectionBuilder\Tag\Block\Adminhtml\Tab;

class ProductGrid extends \SectionBuilder\Product\Block\Adminhtml\Tab\ProductGrid
{
    protected $tagProductCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productCollectionFactory,
        \SectionBuilder\Tag\Model\ResourceModel\TagProduct\CollectionFactory $tagProductCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->tagProductCollectionFactory = $tagProductCollectionFactory;
        parent::__construct($context, $backendHelper, $productCollectionFactory, $moduleManager, $data);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/tag/grids', ['_current' => true]);
    }

    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create()->addFieldToFilter('is_enable', ['eq' => 1]);
        $this->setCollection($collection);
        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareCollection();
    }

    protected function _getSelectedProducts()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return [];
        }

        $collection = $this->tagProductCollectionFactory->create()
            ->addFieldToFilter('tag_id', $this->getRequest()->getParam('id'))
            ->addFieldToSelect('product_id');
        $productIds = $collection->getData();

        return $productIds ? array_column($productIds, 'product_id') : [];
    }
}
