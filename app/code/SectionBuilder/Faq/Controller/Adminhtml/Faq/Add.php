<?php
namespace SectionBuilder\Faq\Controller\Adminhtml\Faq;

use SectionBuilder\Faq\Controller\Adminhtml\Faq;
use Magento\Framework\Controller\ResultFactory;

class Add extends Faq
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}
