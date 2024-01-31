<?php
namespace SectionBuilder\Category\Controller\Adminhtml\Category;

use SectionBuilder\Category\Controller\Adminhtml\Category;

class Form extends Category
{
    public function execute()
    {
        $category = $this->categoryFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $category->load($id);
        }

        $title = __('New Category');
        if (isset($category) && $category->getId()) {
            $title = __("%1", $category->getName());
        }

        $page = $this->pageFactory->create();
//        dd($page->getConfig()->getTitle());
//        $page->getConfig()->getTitle()->prepend($title);
        return $page;
    }
}
