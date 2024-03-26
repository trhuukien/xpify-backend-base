<?php
namespace SectionBuilder\Product\Ui\Component\Listing\Columns;

class Action extends \SectionBuilder\Core\Model\Ui\Component\Listing\Columns\Handle
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = $this->addActionEdit($dataSource, 'section_builder/product/edit');
        $dataSource = $this->addActionDelete($dataSource, 'section_builder/product/delete');

        return $dataSource;
    }
}
