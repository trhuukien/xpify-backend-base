<?php
namespace SectionBuilder\Tag\Controller\Adminhtml\Tag;

use SectionBuilder\Tag\Controller\Adminhtml\Tag;
use Magento\Framework\Controller\ResultFactory;

class Index extends Tag
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('SectionBuilder_Tag::tag_management');
        $resultPage->getConfig()->getTitle()->prepend(__('Tag'));

        return $resultPage;
    }
}
