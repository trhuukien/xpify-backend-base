<?php
declare(strict_types=1);

namespace Xpify\App\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Xpify\App\Api\Data\AppInterface as IApp;

class App extends AbstractDb
{
    const MAIN_TABLE = '$xpify_apps';

    /**
     * Init app resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, IApp::ID);
    }
}
