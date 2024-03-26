<?php
namespace SectionBuilder\Category\Block\Adminhtml\Tab;

class ProductGrid extends \SectionBuilder\Product\Block\Adminhtml\Tab\ProductGrid
{
    protected $categoryProductCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productCollectionFactory,
        \SectionBuilder\Category\Model\ResourceModel\CategoryProduct\CollectionFactory $categoryProductCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $data = []
    ) {
        $this->categoryProductCollectionFactory = $categoryProductCollectionFactory;
        parent::__construct($context, $backendHelper, $productCollectionFactory, $moduleManager, $dataPersistor, $data);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/category/grids', ['_current' => true]);
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

        $collection = $this->categoryProductCollectionFactory->create()
            ->addFieldToFilter('category_id', $this->getRequest()->getParam('id'))
            ->addFieldToSelect('product_id');
        $productIds = $collection->getData();

        return $productIds ? array_column($productIds, 'product_id') : [];
    }
}
