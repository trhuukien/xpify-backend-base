<?php
namespace SectionBuilder\Category\Controller\Adminhtml\Category;

class Grids extends \SectionBuilder\Product\Controller\Adminhtml\Product\ProductGrids
{
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'SectionBuilder\Category\Block\Adminhtml\Tab\ProductGrid',
                'sb.product_list.grid'
            )->toHtml()
        );
    }
}
