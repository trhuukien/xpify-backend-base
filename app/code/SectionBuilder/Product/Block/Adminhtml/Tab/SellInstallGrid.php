<?php
namespace SectionBuilder\Product\Block\Adminhtml\Tab;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

class SellInstallGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $merchantCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Xpify\Merchant\Model\ResourceModel\Merchant\CollectionFactory $merchantCollectionFactory,
        array $data = []
    ) {
        $this->merchantCollectionFactory = $merchantCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid_sell_install');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $collection = $this->merchantCollectionFactory->create();
        $collection->addFieldToFilter('app_id', 4);
        $collection->join(
            ['i' => \SectionBuilder\Product\Model\ResourceModel\SectionInstall::MAIN_TABLE],
            'main_table.shop = i.merchant_shop AND i.product_id = 11',
            ['total_theme_installing' => new \Zend_Db_Expr("COUNT(DISTINCT i.theme_id)")]
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
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
            'shop',
            [
                'header' => __('Shop'),
                'index' => 'shop',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        $this->addColumn(
            'total_theme_installing',
            [
                'header' => __('Installing in theme(s)'),
                'index' => 'total_theme_installing',
                'type' => 'number',
                'header_css_class' => 'col-total_theme_installing',
                'column_css_class' => 'col-total_theme_installing'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/product/sellInstallGrids', ['_current' => true]);
    }
}
