<?php
namespace SectionBuilder\Faq\Controller\Adminhtml\Faq;

use SectionBuilder\Faq\Controller\Adminhtml\Faq;

class Form extends Faq
{
    public function execute()
    {
        $faq = $this->faqFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $faq->load($id);
        }

        $title = __('New Faq');
        if (isset($faq) && $faq->getId()) {
            $title = __("%1", $faq->getName());
        }

        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend($title);
        return $page;
    }
}
