<?php
namespace SectionBuilder\Category\Controller\Adminhtml\Category;

use SectionBuilder\Category\Controller\Adminhtml\Category;
use Magento\Framework\Controller\ResultFactory;

class Index extends Category
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('SectionBuilder_Category::category_management');
        $resultPage->getConfig()->getTitle()->prepend(__('Category'));

        return $resultPage;
    }
}
