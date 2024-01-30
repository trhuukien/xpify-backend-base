<?php
namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use SectionBuilder\Product\Controller\Adminhtml\Product;

class Form extends Product
{
    public function execute()
    {
        $section = $this->sectionFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $section->load($id);
        }

        $title = __('New Section');
        if (isset($section) && $section->getId()) {
            $title = __("%1", $section->getName());
        }

        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend($title);
        return $page;
    }
}
