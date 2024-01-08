<?php
declare(strict_types=1);

namespace Xpify\App\Controller\Adminhtml\Apps;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Xpify\App\Api\AppRepositoryInterface;
use Xpify\App\Api\Data\AppInterface;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    private $dataPersistor;

    private $appRepository;

    /**
     * @param Context $context
     * @param AppRepositoryInterface $appRepository
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        AppRepositoryInterface $appRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->appRepository = $appRepository;
    }

    /**
     * Save app action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPost();
        $postData = $postData->toArray();
        $redirectData = [
            'path' => 'xpify/apps/',
            'params' => []
        ];
        try {
            $this->validate($postData);
            try {
                $app = $this->appRepository->get($postData['entity_id']);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $app = $this->appRepository->newInstance();
            }
            if (isset($postData[AppInterface::CREATED_AT])) {
                unset($postData[AppInterface::CREATED_AT]);
            }
            $app->setName($postData[AppInterface::NAME]);
            $app->setApiKey($postData[AppInterface::API_KEY]);
            $app->setSecretKey($postData[AppInterface::SECRET_KEY]);
            $app = $this->appRepository->save($app);
            if (!$app->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Something went wrong while saving the app'));
            }
            $this->messageManager->addSuccessMessage(__('App has been saved successfully'));
            $redirectData['path'] = 'xpify/apps/edit';
            $redirectData['params'] = ['id' => $app->getId()];

        } catch (\Exception $e) {
            $this->dataPersistor->set('xpify_app', $postData);
            $this->messageManager->addErrorMessage($e->getMessage());
            if (empty($postData['entity_id'])) {
                $redirectData['path'] = 'xpify/apps/new';
            } else {
                $redirectData['path'] = 'xpify/apps/edit';
                $redirectData['params'] = ['id' => $postData['entity_id']];
            }
        }

        return $this->resultRedirectFactory->create()->setPath($redirectData['path'], $redirectData['params']);
    }

    /**
     * Validate data
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validate(array $data)
    {
        if (empty($data['name'])) {
            throw new \Magento\Framework\Exception\InputException(__('App Name is required'));
        }
        // check app name length is less than equal 30 character
        if (strlen($data['name']) > 30) {
            throw new \Magento\Framework\Exception\InputException(__('App Name length should be less than equal 30 character'));
        }
    }
}
