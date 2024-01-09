<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

interface PricingPlanRepositoryInterface
{
    /**
     * Load by id
     *
     * @param $id
     * @return IPricingPlan
     * @throws NoSuchEntityException
     */
    public function get($id): IPricingPlan;

    /**
     * Save
     *
     * @param IPricingPlan $obj
     * @return IPricingPlan
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(IPricingPlan $obj): IPricingPlan;

    /**
     * Delete
     *
     * @param IPricingPlan $obj
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(IPricingPlan $obj): void;

    /**
     * Delete by id
     *
     * @param $id
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById($id): void;
}
