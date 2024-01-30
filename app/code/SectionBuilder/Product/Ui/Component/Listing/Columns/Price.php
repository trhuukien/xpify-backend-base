<?php
namespace SectionBuilder\Product\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class Price extends Column
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
                if (empty($item[$fieldName])) {
                    $item[$fieldName] = __('Free');
                    $item[$fieldName] = "<b>Free</b>";
                } else {
                    $item[$fieldName] = number_format($item[$fieldName], 2) . "$";
                }
            }
        }

        return $dataSource;
    }
}