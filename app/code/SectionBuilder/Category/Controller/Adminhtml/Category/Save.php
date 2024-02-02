<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    protected $dataPersistor;

    protected $categoryRepository;

    protected $messageManager;

    public function __construct(
        Context $context,
        \SectionBuilder\Category\Api\CategoryRepositoryInterface $categoryRepository,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->categoryRepository = $categoryRepository;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray()['general'] ?? [];

        try {
            if (empty($postData['entity_id'])) {
                $category = $this->categoryRepository->create();
            } else {
                $category = $this->categoryRepository->get('entity_id', $postData['entity_id']);
            }

            $category->setIsEnable((int)$postData['is_enable']);
            $category->setName(trim($postData['name']));
            $this->categoryRepository->save($category);

            $this->messageManager->addSuccessMessage(__('You saved the category.'));
            $redirectPath = 'section_builder/category/edit';
            $redirectParams = ['id' => $category->getId()];
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_category_data', $postData);
            if (empty($postData['entity_id'])) {
                $redirectPath = 'section_builder/category/add';
            } else {
                $redirectPath = 'section_builder/category/edit';
                $redirectParams = ['id' => $postData['entity_id']];
            }
        }

        return $this->resultRedirectFactory->create()->setPath(
            $redirectPath,
            $redirectParams ?? []
        );
    }
}
