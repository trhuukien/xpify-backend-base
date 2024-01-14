<?php
declare(strict_types=1);

namespace Xpify\Core\Model\Deprecated\Indexer;

use Magento\Framework\Indexer\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * Get indexers list
     *
     * @return array[]
     */
    public function getIndexers(): array
    {
        return [];
    }

    /**
     * Get indexer by ID
     *
     * @param string $indexerId
     * @return array
     */
    public function getIndexer($indexerId): array
    {
        return [];
    }
}
