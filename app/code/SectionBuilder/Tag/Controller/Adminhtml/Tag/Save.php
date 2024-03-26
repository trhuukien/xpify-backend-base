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

    protected $changeData;
    protected $tagRepository;
    protected $tagProductResource;
    protected $dataPersistor;
    protected $serializer;
    protected $messageManager;

    public function __construct(
        Context $context,
        \SectionBuilder\Core\Model\Change $changeData,
        \SectionBuilder\Tag\Api\TagRepositoryInterface $tagRepository,
        \SectionBuilder\Tag\Model\ResourceModel\TagProduct $tagProductResource,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->changeData = $changeData;
        $this->tagRepository = $tagRepository;
        $this->tagProductResource = $tagProductResource;
        $this->dataPersistor = $dataPersistor;
        $this->serializer = $serializer;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray();

        try {
            if (empty($postData['general']['entity_id'])) {
                $tag = $this->tagRepository->create();
                $redirectEditPage = true;
            } else {
                $tag = $this->tagRepository->get('entity_id', $postData['general']['entity_id']);
            }

            $tag->setIsEnable((int)$postData['general']['is_enable']);
            $tag->setName(trim($postData['general']['name']));
            $this->tagRepository->save($tag);

            $productIds = [];
            if (isset($postData['product_list'])) {
                $childIdsArr = $this->serializer->unserialize($postData['product_list']);
                foreach ($childIdsArr as $productId => $isSelected) {
                    if ($isSelected) {
                        $productIds[] = $productId;
                    }
                }
            }

            $this->changeData->replaceData(
                $this->tagProductResource,
                $productIds,
                'tag_id',
                $tag->getId(),
                'product_id'
            );

            $this->messageManager->addSuccessMessage(__('You saved the tag.'));
            if (!empty($redirectEditPage)) {
                $redirectPath = '*/*/edit';
                $redirectParams = ['id' => $tag->getId()];
                return $this->resultRedirectFactory->create()->setPath(
                    $redirectPath,
                    $redirectParams
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_tag_data', $postData['general']);
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}
