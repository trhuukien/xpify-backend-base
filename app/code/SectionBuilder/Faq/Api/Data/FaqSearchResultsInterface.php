<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Api\Data;

interface FaqSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    public function getItems();

    public function setItems(array $items);
}
