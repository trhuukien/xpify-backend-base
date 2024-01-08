<?php
declare(strict_types=1);

namespace Xpify\App\Controller\Adminhtml\Apps;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Xpify\App\Api\AppRepositoryInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    private $appRepository;

    /**
     * @param Context $context
     * @param AppRepositoryInterface $appRepository
     */
    public function __construct(Context $context, AppRepositoryInterface $appRepository)
    {
        parent::__construct($context);
        $this->appRepository = $appRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $id = (int) $this->getRequest()->getParam('id');
            $this->appRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('App has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while deleting the app.'));
        }
        return $this->resultRedirectFactory->create()->setPath('xpify/apps/', []);
    }
}
