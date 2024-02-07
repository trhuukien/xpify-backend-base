<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    protected $dataPersistor;

    protected $faqRepository;

    protected $messageManager;

    public function __construct(
        Context $context,
        \SectionBuilder\Faq\Api\FaqRepositoryInterface $faqRepository,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->faqRepository = $faqRepository;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray()['general'] ?? [];

        try {
            if (empty($postData['entity_id'])) {
                $faq = $this->faqRepository->create();
            } else {
                $faq = $this->faqRepository->get('entity_id', $postData['entity_id']);
            }

            $faq->setIsEnable((int)$postData['is_enable']);
            $faq->setTitle(trim($postData['title']));
            $faq->setContent($postData['content']);
            $this->faqRepository->save($faq);

            $this->messageManager->addSuccessMessage(__('You saved the faq.'));
            $redirectPath = '*/*/';
            $redirectParams = ['id' => $faq->getId()];
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_faq_data', $postData);
            if (empty($postData['entity_id'])) {
                $redirectPath = '*/*/add';
            } else {
                $redirectPath = '*/*/';
                $redirectParams = ['id' => $postData['entity_id']];
            }
        }

        return $this->resultRedirectFactory->create()->setPath(
            $redirectPath,
            $redirectParams ?? []
        );
    }
}
