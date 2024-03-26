<?php
namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

class SellInstallGrids extends \Magento\Backend\App\Action
{
    protected $resultRawFactory;

    protected $layoutFactory;

    public function __construct(
        Context $context,
        Rawfactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'SectionBuilder\Product\Block\Adminhtml\Tab\SellInstallGrid',
                'sb.sell_install'
            )->toHtml()
        );
    }
}
