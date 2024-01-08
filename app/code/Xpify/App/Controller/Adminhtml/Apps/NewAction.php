<?php
declare(strict_types=1);

namespace Xpify\App\Controller\Adminhtml\Apps;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class NewAction extends Edit implements HttpGetActionInterface
{
    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->rsForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}
