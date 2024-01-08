<?php
declare(strict_types=1);

namespace Xpify\App\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Xpify\App\Api\AppRepositoryInterface;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\App\Model\ResourceModel\App;

class AppRepository implements AppRepositoryInterface
{
    private $resource;
    private $appFactory;
    private $logger;

    /**
     * @param App $resource
     * @param AppFactory $appFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Xpify\App\Model\ResourceModel\App $resource,
        \Xpify\App\Model\AppFactory $appFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->appFactory = $appFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function get($value, $field = 'entity_id')
    {
        $app = $this->newInstance();
        try {
            $this->resource->load($app, $value, $field);
        } catch (\Throwable $e) {
            $this->logger->debug($e->getMessage());
            throw new NoSuchEntityException(__('Unable to find app with ID "%1"', $value));
        }
        if (!$app->getId()) {
            throw new NoSuchEntityException(__('Unable to find app with ID "%1"', $value));
        }
        return $app;
    }

    /**
     * @inheritDoc
     */
    public function save(IApp $app)
    {
        try {
            $this->resource->save($app);
            return $app;
        } catch (\Throwable $e) {
            $this->logger->debug($e->getMessage());
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the app: %1', $e->getMessage()),
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(IApp $app)
    {
        try {
            $this->resource->delete($app);
            return true;
        } catch (\Throwable $e) {
            $this->logger->debug($e->getMessage());
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __('Could not delete the app: %1', $e->getMessage()),
                $e
            );
        }
    }

    public function deleteById(mixed $id)
    {
        try {
            return $this->delete($this->get($id));
        } catch (NoSuchEntityException $e) {
            throw $e;
        }
    }

    /**
     * Create new app
     *
     * @return IApp
     */
    public function newInstance()
    {
        return $this->appFactory->create();
    }
}
