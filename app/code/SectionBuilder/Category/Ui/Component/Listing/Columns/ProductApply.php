<?php
namespace SectionBuilder\Category\Ui\Component\Listing\Columns;

class ProductApply extends \SectionBuilder\Core\Model\Ui\Component\Listing\Columns\Handle
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        return $this->handleCountProduct($dataSource, 'category_id');
    }
}
