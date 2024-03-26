<?php
namespace SectionBuilder\Product\Block\Adminhtml\Tab;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

class ProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;

    protected $dataPersistor;

    protected $productFactory;

    protected $productCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->moduleManager = $moduleManager;
        $this->dataPersistor = $dataPersistor;
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
        }
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('type_id', ['eq' => \SectionBuilder\Product\Model\Config\Source\ProductType::SIMPLE_TYPE_ID])
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
        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => [
                    '' => __(' '),
                    1 => __('Simple'),
                    2 => __('Group')
                ],
                'filter' => \Magento\Backend\Block\Widget\Grid\Column\Filter\Select::class,
                'header_css_class' => 'col-type_id',
                'column_css_class' => 'col-type_id'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/product/productGrids', ['_current' => true]);
    }

    protected function _getSelectedProducts()
    {
        $dataPersistor = $this->dataPersistor->get('section_product_data');
        if (isset($dataPersistor['product_list'])) {
            $childIds = $dataPersistor['product_list'];
        } else {
            $collection = $this->productCollectionFactory->create()
                ->addFieldToFilter('entity_id', $this->getRequest()->getParam('id'))
                ->addFieldToSelect('child_ids');
            $childIds = $collection->getFirstItem()->getChildIds() ?? "";
        }

        return explode(",", $childIds);
    }
}
