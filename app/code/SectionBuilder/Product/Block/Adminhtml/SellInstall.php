<?php
namespace SectionBuilder\Product\Block\Adminhtml;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class SellInstall extends \Magento\Backend\Block\Template
{
    protected $_template = 'SectionBuilder_Product::products/sell_install.phtml';

    protected $blockGrid;

    protected $productCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $productCollectionFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'SectionBuilder\Product\Block\Adminhtml\Tab\SellInstallGrid',
                'sb.sell_install'
            );
        }
        return $this->blockGrid;
    }

    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    public function getTotalRecord()
    {
        $productId = $this->getRequest()->getParam('id');
        if ($productId) {
            $collection = $this->productCollectionFactory->create();
            $collection->addFieldToFilter('main_table.entity_id', $productId);
            $collection->addFieldToSelect(['installed' => 'qty_installed']);
            $collection->getSelect()->joinLeft(
                ['b' => \SectionBuilder\Product\Model\ResourceModel\SectionBuy::MAIN_TABLE],
                'main_table.entity_id = b.product_id',
                ['bought' => new \Zend_Db_Expr("COUNT(DISTINCT CASE WHEN b.product_id = $productId THEN b.product_id ELSE NULL END)")]
            );
            $collection->getSelect()->joinLeft(
                ['i' => \SectionBuilder\Product\Model\ResourceModel\SectionInstall::MAIN_TABLE],
                'main_table.entity_id = i.product_id',
                [
                    'theme_installing' => new \Zend_Db_Expr("COUNT(DISTINCT CASE WHEN i.product_id = $productId THEN i.theme_id ELSE NULL END)"),
                    'shop_installing' => new \Zend_Db_Expr("COUNT(DISTINCT CASE WHEN i.product_id = $productId THEN i.merchant_shop ELSE NULL END)")
                ]
            );
            $collection->addFieldToFilter(['i.product_id', 'b.product_id'], [$productId, $productId]);
            $collection->getSelect()->group('main_table.entity_id');
            return $collection->getFirstItem()->getData();
        }

        return [];
    }
}
