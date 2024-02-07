<?php
namespace SectionBuilder\Faq\Controller\Adminhtml\Faq;

use SectionBuilder\Faq\Controller\Adminhtml\Faq;

class Edit extends Faq
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
