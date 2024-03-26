<?php
namespace SectionBuilder\Product\Block\Adminhtml\Tab;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

class SellInstallGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $config;

    protected $merchantCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \SectionBuilder\Core\Model\Config $config,
        \Xpify\Merchant\Model\ResourceModel\Merchant\CollectionFactory $merchantCollectionFactory,
        array $data = []
    ) {
        $this->config = $config;
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
        $productId = $this->getRequest()->getParam('id');
        $appId = $this->config->getAppConnectingId(true);
        if ($productId && $appId) {
            $collection = $this->merchantCollectionFactory->create();
            $collection->addFieldToFilter('app_id', $appId);
            $collection->addFieldToSelect(['shop', 'user_email']);
            $collection->getSelect()->joinLeft(
                ['b' => \SectionBuilder\Product\Model\ResourceModel\SectionBuy::MAIN_TABLE],
                'main_table.shop = b.merchant_shop',
                ['buy_on' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT CASE WHEN b.product_id = $productId THEN b.created_at ELSE NULL END)")]
            );
            $collection->getSelect()->joinLeft(
                ['i' => \SectionBuilder\Product\Model\ResourceModel\SectionInstall::MAIN_TABLE],
                'main_table.shop = i.merchant_shop',
                ['theme_apply' => new \Zend_Db_Expr("COUNT(DISTINCT CASE WHEN i.product_id = $productId THEN i.theme_id ELSE NULL END)")]
            );
            $collection->addFieldToFilter(['i.product_id', 'b.product_id'], [$productId, $productId]);
            $collection->getSelect()->group('main_table.shop');
            $this->setCollection($collection);
        }

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
                'filter' => false,
                'header_css_class' => 'col-id-product',
                'column_css_class' => 'col-id-product'
            ]
        );
        $this->addColumn(
            'shop',
            [
                'header' => __('Merchant Shop'),
                'index' => 'shop',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        $this->addColumn(
            'user_email',
            [
                'header' => __('Merchant Email'),
                'index' => 'user_email',
                'header_css_class' => 'col-user_email',
                'column_css_class' => 'col-user_email'
            ]
        );
        $this->addColumn(
            'theme_apply',
            [
                'header' => __('Theme Installing'),
                'index' => 'theme_apply',
                'type' => 'number',
                'filter' => false,
                'header_css_class' => 'col-total_theme_installing',
                'column_css_class' => 'col-total_theme_installing'
            ]
        );
        $this->addColumn(
            'buy_on',
            [
                'header' => __('Buy on'),
                'index' => 'buy_on',
                'type' => 'date',
                'filter' => false,
                'header_css_class' => 'col-buy_on',
                'column_css_class' => 'col-buy_on'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/product/sellInstallGrids', ['_current' => true]);
    }
}
