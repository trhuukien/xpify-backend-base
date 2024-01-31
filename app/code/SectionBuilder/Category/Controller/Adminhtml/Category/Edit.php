<?php
namespace SectionBuilder\Category\Controller\Adminhtml\Category;

use SectionBuilder\Category\Controller\Adminhtml\Category;

class Edit extends Category
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
