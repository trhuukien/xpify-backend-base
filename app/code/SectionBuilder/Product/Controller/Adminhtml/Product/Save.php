<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Controller\Adminhtml\Product;

use SectionBuilder\Product\Model\Helper\Image;

class Save extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    protected $changeData;

    protected $sectionRepository;

    protected $categoryProductResource;

    protected $tagProductResource;

    protected $imageHelper;

    protected $getFileRaw;

    protected $dataPersistor;

    protected $messageManager;

    protected $serializer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SectionBuilder\Core\Model\Change $changeData,
        \SectionBuilder\Product\Api\SectionRepositoryInterface $sectionRepository,
        \SectionBuilder\Category\Model\ResourceModel\CategoryProduct $categoryProductResource,
        \SectionBuilder\Tag\Model\ResourceModel\TagProduct $tagProductResource,
        \SectionBuilder\Product\Model\Helper\Image $imageHelper,
        \SectionBuilder\FileModifier\Model\GetFileRaw $getFileRaw,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->changeData = $changeData;
        $this->sectionRepository = $sectionRepository;
        $this->categoryProductResource = $categoryProductResource;
        $this->tagProductResource = $tagProductResource;
        $this->imageHelper = $imageHelper;
        $this->getFileRaw = $getFileRaw;
        $this->dataPersistor = $dataPersistor;
        $this->messageManager = $messageManager;
        $this->serializer = $serializer;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray();

        try {
            if (empty($postData['entity_id'])) {
                $section = $this->sectionRepository->create();
                $redirectEditPage = true;
            } else {
                $section = $this->sectionRepository->get('entity_id', $postData['entity_id']);
            }

            $section->setIsEnable((int)$postData['is_enable']);
            $section->setName(trim($postData['name']));
            $section->setKey(trim($postData['url_key']));
            $section->setPrice((float)$postData['price']);
            $section->setShortDescription($postData['short_description']);
            $section->setDescription($postData['description']);

            $mediaGallery = $postData['media_gallery'] ?? '';
            if (is_array($mediaGallery)) {
                $galleryArray = $this->imageHelper->uploadMediaGallery($postData['media_gallery']);
                $mediaGallery = implode(\SectionBuilder\Product\Model\Helper\Image::SEPARATION, $galleryArray);
            }
            $postData['media_gallery'] = $mediaGallery;
            $section->setMediaGallery($postData['media_gallery']);

            $postData['is_group_product'] = (int)$postData['is_group_product'];
            if (!$postData['is_group_product']) {
                $section->setTypeId(\SectionBuilder\Product\Model\Config\Source\ProductType::SIMPLE_TYPE_ID);
                $section->setVersion(trim($postData['version']));
                $section->setPlanId($postData['plan_id'] ?: null);
                $section->setSrc($postData['src'] ?: null);
                $section->setPathSource(ltrim($postData['path_source'], '/') ?: null);
                $section->setReleaseNote($postData['release_note']);
                $section->setDemoLink($postData['demo_link']);

                if ($this->getFileRaw->execute($section->getPathSource()) === '') {
                    $this->messageManager->addErrorMessage(_('The source code is empty!'));
                    $this->dataPersistor->set('section_product_data', $postData);
                    return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
                }
            } else {
                $section->setTypeId(\SectionBuilder\Product\Model\Config\Source\ProductType::GROUP_TYPE_ID);
                $childIdsArr = $this->serializer->unserialize($postData['product_list']);
                $childIds = [];
                foreach ($childIdsArr as $entityId => $isSelected) {
                    if ($isSelected) {
                        $childIds[] = $entityId;
                    }
                }
                $postData['product_list'] = implode(",", $childIds);
                $section->setChildIds($postData['product_list']);

                if (!$section->getChildIds()) {
                    $this->messageManager->addErrorMessage(__('Please select child product!'));
                    $this->dataPersistor->set('section_product_data', $postData);
                    return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
                }
            }

            $this->sectionRepository->save($section);

            $this->changeData->replaceData(
                $this->categoryProductResource,
                $postData['categories'] ?? [],
                'product_id',
                $section->getId(),
                'category_id'
            );
            $this->changeData->replaceData(
                $this->tagProductResource,
                $postData['tags'] ?? [],
                'product_id',
                $section->getId(),
                'tag_id'
            );

            $this->messageManager->addSuccessMessage(__('You saved the product.'));
            if (!empty($redirectEditPage)) {
                $redirectPath = '*/*/edit';
                $redirectParams = ['id' => $section->getId()];
                return $this->resultRedirectFactory->create()->setPath(
                    $redirectPath,
                    $redirectParams
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->dataPersistor->set('section_product_data', $postData);
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }
}
