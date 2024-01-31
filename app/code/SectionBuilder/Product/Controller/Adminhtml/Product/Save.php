<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    protected $sectionRepository;

    protected $categoryProductResource;

    protected $tagProductResource;

    protected $dataPersistor;

    protected $messageManager;

    public function __construct(
        Context $context,
        \SectionBuilder\Product\Api\SectionRepositoryInterface $sectionRepository,
        \SectionBuilder\Category\Model\ResourceModel\CategoryProduct $categoryProductResource,
        \SectionBuilder\Tag\Model\ResourceModel\TagProduct $tagProductResource,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->sectionRepository = $sectionRepository;
        $this->categoryProductResource = $categoryProductResource;
        $this->tagProductResource = $tagProductResource;
        $this->dataPersistor = $dataPersistor;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray();

        try {
            if (empty($postData['entity_id'])) {
                $section = $this->sectionRepository->create();
            } else {
                $section = $this->sectionRepository->get('entity_id', $postData['entity_id']);
            }

            $section->setIsEnable((int)$postData['is_enable']);
            $section->setName(trim($postData['name']));
            $section->setPrice((float)$postData['price']);
            $section->setSrc(trim($postData['src']));
            $section->setFileData($postData['file_data']);
            $this->sectionRepository->save($section);

            $this->replaceCategories($section->getId(), $postData['categories'] ?? []);
            $this->replaceTags($section->getId(), $postData['tags'] ?? []);

            $this->messageManager->addSuccessMessage(__('You saved the product.'));
            $redirectPath = 'section_builder/product/edit';
            $redirectParams = ['id' => $section->getId()];
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_product_data', $postData);
            if (empty($postData['entity_id'])) {
                $redirectPath = 'section_builder/product/add';
            } else {
                $redirectPath = 'section_builder/product/edit';
                $redirectParams = ['id' => $postData['entity_id']];
            }
        }

        return $this->resultRedirectFactory->create()->setPath(
            $redirectPath,
            $redirectParams ?? []
        );
    }

    public function replaceCategories($productId, $categories)
    {
        $this->categoryProductResource->getConnection()->delete(
            $this->categoryProductResource->getMainTable(),
            ['product_id = ?' => $productId]
        );

        foreach ($categories as $categoryId) {
            if ($categoryId) {
                $dataToInsert[] = [
                    'product_id' => $productId,
                    'category_id' => $categoryId
                ];
            }
        }
        if (!empty($dataToInsert)) {
            $this->categoryProductResource->getConnection()->insertMultiple(
                $this->categoryProductResource->getMainTable(),
                $dataToInsert
            );
        }
    }

    public function replaceTags($productId, $tagIds)
    {
        $this->tagProductResource->getConnection()->delete(
            $this->tagProductResource->getMainTable(),
            ['product_id = ?' => $productId]
        );

        foreach ($tagIds as $tagId) {
            if ($tagId) {
                $dataToInsert[] = [
                    'product_id' => $productId,
                    'tag_id' => $tagId
                ];
            }
        }
        if (!empty($dataToInsert)) {
            $this->tagProductResource->getConnection()->insertMultiple(
                $this->tagProductResource->getMainTable(),
                $dataToInsert
            );
        }
    }
}
