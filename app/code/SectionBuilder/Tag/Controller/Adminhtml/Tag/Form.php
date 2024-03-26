<?php
namespace SectionBuilder\Tag\Controller\Adminhtml\Tag;

use SectionBuilder\Tag\Controller\Adminhtml\Tag;

class Form extends Tag
{
    public function execute()
    {
        $section = $this->sectionFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $section->load($id);
        }

        $title = __('New Tag');
        if (isset($section) && $section->getId()) {
            $title = __("%1", $section->getName());
        }

        $page = $this->pageFactory->create();
        $page->setActiveMenu('SectionBuilder_Product::product_management');
        $page->getConfig()->getTitle()->prepend($title);
        return $page;
    }
}
