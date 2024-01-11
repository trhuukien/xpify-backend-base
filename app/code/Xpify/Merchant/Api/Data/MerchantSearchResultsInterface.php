<?php
declare(strict_types=1);

namespace Xpify\Merchant\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

interface MerchantSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get merchant list.
     *
     * @return IMerchant[]
     */
    public function getItems();

    /**
     * Set merchant list.
     *
     * @param IMerchant[] $items
     * @return $this
     */
    public function setItems(array $items);
}
