<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Model;

use Psr\Log\LoggerInterface;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface;
use Xpify\PricingPlan\Model\ResourceModel\PricingPlan;

class PricingPlanRepository implements PricingPlanRepositoryInterface
{
    private PricingPlan $resource;

    private PricingPlanFactory $factory;

    private \Psr\Log\LoggerInterface $logger;

    /**
     * @param PricingPlan $resource
     * @param PricingPlanFactory $factory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceModel\PricingPlan $resource,
        PricingPlanFactory $factory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * Create new instance of Pricing Plan
     *
     * @return IPricingPlan
     */
    public function create(): IPricingPlan
    {
        return $this->factory->create();
    }

    /**
     * @inheritDoc
     */
    public function get($id): IPricingPlan
    {
        $obj = $this->create();
        $this->resource->load($obj, $id);
        if (!$obj->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Unable to find pricing plan with ID "%1"', $id));
        }
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function save(IPricingPlan $obj): IPricingPlan
    {
        try {
            $this->resource->save($obj);
            return $obj;
        } catch (\Throwable $e) {
            $this->logger->debug($e);
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save pricing plan.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(IPricingPlan $obj): void
    {
        try {
            $this->resource->delete($obj);
        } catch (\Throwable $e) {
            $this->logger->debug($e);
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Unable to remove pricing plan.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id): void
    {
        try {
            $this->delete($this->get($id));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // it ok.
        }
    }
}
