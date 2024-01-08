<?php
declare(strict_types=1);

namespace Xpify\Core\DisableSearchEngine;

class SearchConfigOptionsList
{
    public function afterGetAvailableSearchEngineList($subject, $result)
    {
        return array_merge($result, ['none' => 'No Search Engine']);
    }
}
