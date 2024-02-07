<?php
namespace SectionBuilder\Faq\Ui\Component\Listing\Columns;

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
        return $this->handleAction($dataSource, 'section_builder/faq/edit');
    }
}
