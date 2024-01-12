<?php
declare(strict_types=1);

namespace Xpify\App\Api\Data;

use Magento\Framework\Api\SearchResultsInterface as BaseSearchResultsInterface;

interface AppSearchResultsInterface extends BaseSearchResultsInterface
{
    /**
     * @return AppInterface[]
     */
    public function getItems();

    /**
     * @param AppInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
