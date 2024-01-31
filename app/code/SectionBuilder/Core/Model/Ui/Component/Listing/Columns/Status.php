<?php
namespace SectionBuilder\Category\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getName();
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item[$fieldName]) {
                    $item[$fieldName] = __('Enable');
                } else {
                    $item[$fieldName] = __('Disable');
                }
            }
        }

        return $dataSource;
    }
}
