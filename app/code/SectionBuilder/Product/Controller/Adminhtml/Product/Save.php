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

    protected $dataPersistor;

    protected $sectionRepository;

    protected $messageManager;

    public function __construct(
        Context $context,
        \SectionBuilder\Product\Api\SectionRepositoryInterface $sectionRepository,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->sectionRepository = $sectionRepository;
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

            //$section->setIsEnable($postData['is_enable']);
            $section->setName(trim($postData['name']));
            $section->setPrice((float)$postData['price']);
            $section->setSrc(trim($postData['src']));
            $section->setFileData($postData['file_data']);
            $this->sectionRepository->save($section);

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
}
