<?php
declare(strict_types=1);

namespace Xpify\Merchant\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as IMerchantSubscription;

interface MerchantSubscriptionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get merchant subscription list.
     *
     * @return IMerchantSubscription[]
     */
    public function getItems();

    /**
     * Set merchant subscription list.
     *
     * @param IMerchantSubscription[] $items
     * @return $this
     */
    public function setItems(array $items);
}
