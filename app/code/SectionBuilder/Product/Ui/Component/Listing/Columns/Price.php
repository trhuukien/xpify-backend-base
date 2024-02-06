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
                if ($item[$fieldName] == 0) {
                    $item[$fieldName] = "<b style='color: green'>Free</b>";
                } else {
                    $item[$fieldName] = "$" . number_format($item[$fieldName], 2);
                }
            }
        }

        return $dataSource;
    }
}
