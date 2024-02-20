<?php
namespace SectionBuilder\Product\Block\Adminhtml\Tab;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $coreRegistry = null;

    protected $productFactory;

    protected $productCollFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \SectionBuilder\Product\Model\SectionFactory $productFactory,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productCollFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Visibility $visibility = null,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->productCollFactory = $productCollFactory;
        $this->coreRegistry = $coreRegistry;
        $this->moduleManager = $moduleManager;
        $this->_storeManager = $storeManager;
        $this->visibility = $visibility ?: ObjectManager::getInstance()->get(Visibility::class);
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid_group_products');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_products' => 1]);
        } else {
            $this->setDefaultFilter(['in_products' => 0]);
        }
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $collection = $this->productCollFactory->create()
            ->addFieldToFilter('type_id', ['eq' => 1])
            ->addFieldToFilter('is_enable', ['eq' => 1]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_products',
            [
                'type' => 'checkbox',
                'html_name' => 'products_id',
                'required' => true,
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'type' => 'number',
                'header_css_class' => 'col-id-product',
                'column_css_class' => 'col-id-product'
            ]
        );
        $this->addColumn(
            'media_gallery',
            [
                'header' => __('Media Gallery'),
                'index' => 'media_gallery',
                'renderer'  => '\SectionBuilder\Product\Block\Adminhtml\Grid\Renderer\Image',
                'header_css_class' => 'col-media_gallery',
                'column_css_class' => 'col-media_gallery'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        $this->addColumn(
            'url_key',
            [
                'header' => __('Key'),
                'index' => 'url_key',
                'header_css_class' => 'col-url_key',
                'column_css_class' => 'col-url_key'
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'price',
                'currency_code' => \Xpify\App\Api\Data\AppInterface::CURRENCY_CODE,
                'index' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/index/grids', ['_current' => true]);
    }

    protected function _getSelectedProducts()
    {
        $collection = $this->productCollFactory->create()
            ->addFieldToFilter('entity_id', $this->getRequest()->getParam('id'))
            ->addFieldToSelect('child_ids');
        $childIds = $collection->getFirstItem()->getChildIds() ?? "";

        return explode(",", $childIds);
    }
}
