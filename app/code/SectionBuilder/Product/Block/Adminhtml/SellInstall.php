<?php
namespace SectionBuilder\Product\Block\Adminhtml;

class SellInstall extends \Magento\Backend\Block\Template
{
    protected $_template = 'SectionBuilder_Product::products/sell_install.phtml';

    protected $blockGrid;

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
}
