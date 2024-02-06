<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    protected $dataPersistor;

    protected $tagRepository;

    protected $messageManager;

    public function __construct(
        Context $context,
        \SectionBuilder\Tag\Api\TagRepositoryInterface $tagRepository,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->tagRepository = $tagRepository;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray()['general'] ?? [];

        try {
            if (empty($postData['entity_id'])) {
                $tag = $this->tagRepository->create();
            } else {
                $tag = $this->tagRepository->get('entity_id', $postData['entity_id']);
            }

            $tag->setIsEnable((int)$postData['is_enable']);
            $tag->setName(trim($postData['name']));
            $this->tagRepository->save($tag);

            $this->messageManager->addSuccessMessage(__('You saved the tag.'));
            $redirectPath = '*/*/';
            $redirectParams = ['id' => $tag->getId()];
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_tag_data', $postData);
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
