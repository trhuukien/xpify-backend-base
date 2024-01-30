<?php
namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use SectionBuilder\Product\Controller\Adminhtml\Product;

class Edit extends Product
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultForward = $this->forwardFactory->create();
        $resultForward->forward('form');
        return $resultForward;
    }
}
