<?php
namespace SectionBuilder\Faq\Controller\Adminhtml\Faq;

use SectionBuilder\Faq\Controller\Adminhtml\Faq;
use Magento\Framework\Controller\ResultFactory;

class Index extends Faq
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('SectionBuilder_Faq::faq_management');
        $resultPage->getConfig()->getTitle()->prepend(__('Faq'));

        return $resultPage;
    }
}
