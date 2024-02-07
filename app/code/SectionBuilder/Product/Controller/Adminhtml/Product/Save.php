<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;

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
        \Magento\Framework\Message\ManagerInterface $messageManager,

        File $fileDriver,
        Filesystem $filesystem,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
    ) {
        parent::__construct($context);
        $this->sectionRepository = $sectionRepository;
        $this->categoryProductResource = $categoryProductResource;
        $this->tagProductResource = $tagProductResource;
        $this->dataPersistor = $dataPersistor;
        $this->messageManager = $messageManager;

        $this->fileDriver = $fileDriver;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
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
            $section->setKey(trim($postData['url_key']));
            $section->setVersion(trim($postData['version']));
            $section->setPrice((float)$postData['price']);
            $section->setPlanId($postData['plan_id'] ?: null);
            $section->setSrc(trim($postData['src']));
            $section->setFileData($postData['file_data']);
            $section->setDescription($postData['description']);
            $section->setReleaseNote($postData['release_note']);
            $section->setDemoLink($postData['demo_link']);
            $galleryArray = $this->uploadMediaGallery($postData);
            if (!empty($galleryArray)) {
                $mediaGallery = implode(\SectionBuilder\Product\Model\Helper\Image::SEPARATION, $galleryArray);
                $section->setMediaGallery($mediaGallery);
            }
            $this->sectionRepository->save($section);

            $this->replaceCategories($section->getId(), $postData['categories'] ?? []);
            $this->replaceTags($section->getId(), $postData['tags'] ?? []);

            $this->messageManager->addSuccessMessage(__('You saved the product.'));
            $redirectPath = '*/*/edit';
            $redirectParams = ['id' => $section->getId()];
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_product_data', $postData);
            if (empty($postData['entity_id'])) {
                $redirectPath = '*/*/add';
            } else {
                $redirectPath = '*/*/edit';
                $redirectParams = ['id' => $postData['entity_id']];
            }
        }

        return $this->resultRedirectFactory->create()->setPath(
            $redirectPath,
            $redirectParams ?? []
        );
    }

    public function uploadMediaGallery($data)
    {
        $bannerimageDirPath = $this->mediaDirectory->getAbsolutePath("section_builder/product");
        $tmp = $this->mediaDirectory->getAbsolutePath(\SectionBuilder\Product\Model\Helper\Image::SUB_DIR . "tmp");
        if (!$this->fileDriver->isExists($bannerimageDirPath)) {
            $this->fileDriver->createDirectory($bannerimageDirPath);
            $this->fileDriver->createDirectory($tmp);
        }
        $gallery = [];

        if (!empty($data['media_gallery']['images'])) {
            $images = $data['media_gallery']['images'];
            foreach ($images as $image) {
                if (empty($image['removed'])) {
                    if (!empty($image['value_id'])) {
                        $gallery[] = $image['value_id'];
                    } elseif (!empty($image['file'])) {
                        $originalImageName = $image['file'];
                        $imageName = $originalImageName;
                        $basePath = "section_builder/product";
                        $baseTmpImagePath = "catalog/tmp/category/" . $imageName;
                        $baseImagePath = $basePath . "/" . $imageName;
                        $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
                        $baseImageAbsolutePath = $mediaPath . $baseImagePath;
                        $i = 1;
                        while (file_exists($baseImageAbsolutePath)) {
                            $i++;
                            $p = mb_strrpos($originalImageName, '.');
                            if (false !== $p) {
                                $imageName = mb_substr($originalImageName, 0, $p) . $i . mb_substr($originalImageName, $p);
                            } else {
                                $imageName = $originalImageName . $i;
                            }
                            $baseImagePath = $basePath . "/" . $imageName;
                            $baseImageAbsolutePath = $mediaPath . $baseImagePath;
                        }
                        $this->coreFileStorageDatabase->copyFile(
                            $baseTmpImagePath,
                            $baseImagePath
                        );
                        $this->mediaDirectory->renameFile(
                            $baseTmpImagePath,
                            $baseImagePath
                        );

                        $gallery[] = $baseImagePath;
                    }
                }
            }
        }

        return $gallery;
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
