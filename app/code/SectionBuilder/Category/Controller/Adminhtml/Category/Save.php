<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use SectionBuilder\Core\Model\Change;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    protected $changeData;

    protected $categoryRepository;

    protected $categoryProductResource;

    protected $dataPersistor;

    protected $serializer;

    protected $messageManager;

    public function __construct(
        Context $context,
        \SectionBuilder\Core\Model\Change $changeData,
        \SectionBuilder\Category\Api\CategoryRepositoryInterface $categoryRepository,
        \SectionBuilder\Category\Model\ResourceModel\CategoryProduct $categoryProductResource,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->changeData = $changeData;
        $this->categoryRepository = $categoryRepository;
        $this->categoryProductResource = $categoryProductResource;
        $this->dataPersistor = $dataPersistor;
        $this->serializer = $serializer;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray();

        try {
            if (empty($postData['general']['entity_id'])) {
                $category = $this->categoryRepository->create();
                $redirectEditPage = true;
            } else {
                $category = $this->categoryRepository->get('entity_id', $postData['general']['entity_id']);
            }

            $category->setIsEnable((int)$postData['general']['is_enable']);
            $category->setName(trim($postData['general']['name']));
            $this->categoryRepository->save($category);

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
                $this->categoryProductResource,
                $productIds,
                'category_id',
                $category->getId(),
                'product_id'
            );

            $this->messageManager->addSuccessMessage(__('You saved the category.'));
            if (!empty($redirectEditPage)) {
                $redirectPath = '*/*/edit';
                $redirectParams = ['id' => $category->getId()];
                return $this->resultRedirectFactory->create()->setPath(
                    $redirectPath,
                    $redirectParams
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_category_data', $postData['general']);
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}
