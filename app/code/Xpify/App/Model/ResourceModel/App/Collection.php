<?php
declare(strict_types=1);

namespace Xpify\App\Model\ResourceModel\App;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Xpify\App\Api\Data\AppInterface as IApp;

class Collection extends AbstractCollection
{
    protected $_idFieldName = IApp::ID;

    /**
     * Define collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Xpify\App\Model\App::class,
            \Xpify\App\Model\ResourceModel\App::class
        );
    }
}
