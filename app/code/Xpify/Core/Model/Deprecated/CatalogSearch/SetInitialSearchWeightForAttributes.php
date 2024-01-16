<?php
declare(strict_types=1);

namespace Xpify\Core\Model\Deprecated\CatalogSearch;

class SetInitialSearchWeightForAttributes extends \Magento\CatalogSearch\Setup\Patch\Data\SetInitialSearchWeightForAttributes
{
    public function apply()
    {
        return $this;
    }
}
