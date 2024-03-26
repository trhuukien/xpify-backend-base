<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Controller\Adminhtml\Product;

class Delete extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    protected $sectionRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SectionBuilder\Product\Api\SectionRepositoryInterface $sectionRepository,
    ) {
        $this->sectionRepository = $sectionRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $section = $this->sectionRepository->get('entity_id', $id);
            if (!$section['qty_installed']) {
                $this->sectionRepository->delete($section);
                $this->messageManager->addSuccessMessage(__('You deleted the product.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
