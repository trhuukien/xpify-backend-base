<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Api\Data;

interface SectionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    public function getItems();

    public function setItems(array $items);
}
