<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model;

use Magento\Framework\Api\SearchResults;
use Xpify\Merchant\Api\Data\MerchantSubscriptionSearchResultsInterface as IMerchantSearchResults;

class SubscriptionSearchResults extends SearchResults implements IMerchantSearchResults
{

}
