<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model\ResourceModel\Merchant;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = \Xpify\Merchant\Api\Data\MerchantInterface::ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Xpify\Merchant\Model\Merchant::class,
            \Xpify\Merchant\Model\ResourceModel\Merchant::class
        );
    }
}
