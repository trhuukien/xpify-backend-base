<?php
namespace SectionBuilder\Tag\Controller\Adminhtml\Tag;

class Grids extends \SectionBuilder\Product\Controller\Adminhtml\Product\ProductGrids
{
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'SectionBuilder\Tag\Block\Adminhtml\Tab\ProductGrid',
                'sb.product_list.grid'
            )->toHtml()
        );
    }
}
