<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Api\Data;

interface TagSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    public function getItems();

    public function setItems(array $items);
}
