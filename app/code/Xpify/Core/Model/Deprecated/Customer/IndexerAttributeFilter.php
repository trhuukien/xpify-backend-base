<?php
declare(strict_types=1);

namespace Xpify\Core\Model\Deprecated\Customer;

class IndexerAttributeFilter extends \Magento\Customer\Model\Indexer\Attribute\Filter
{
    public function filter(array $attributes)
    {
        return $attributes;
    }
}
