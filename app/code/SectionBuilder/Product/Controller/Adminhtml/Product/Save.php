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
            $section->setPlanId($postData['plan_id'] ?: null);
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

    public function replaceCategories($productId, $data)
    {
        $this->replaceData(
            $this->categoryProductResource,
            $data,
            $productId,
            'category_id'
        );
    }

    public function replaceTags($productId, $data)
    {
        $this->replaceData(
            $this->tagProductResource,
            $data,
            $productId,
            'tag_id'
        );
    }

    public function replaceData($resourceModel, $data, $productId, $key)
    {
        $tableName = $resourceModel->getMainTable();
        $select = $resourceModel->getConnection()->select()
            ->from($tableName)
            ->where('product_id = ?', $productId);
        $oldData = $resourceModel->getConnection()->fetchAll($select);
        $oldData = array_column($oldData, $key);

        $dataToRemove = array_diff($oldData, $data);
        $dataToAdd = array_diff($data, $oldData);

        $resourceModel->getConnection()->delete(
            $tableName,
            [
                'product_id = ?' => $productId,
                "$key IN (?)" => $dataToRemove
            ]
        );

        foreach ($dataToAdd as $id) {
            if ($id) {
                $dataToInsert[] = [
                    'product_id' => $productId,
                    "$key" => $id
                ];
            }
        }
        if (!empty($dataToInsert)) {
            $resourceModel->getConnection()->insertMultiple(
                $tableName,
                $dataToInsert
            );
        }
    }
}
