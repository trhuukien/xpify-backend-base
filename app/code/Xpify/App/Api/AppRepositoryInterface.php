<?php
declare(strict_types=1);

namespace Xpify\App\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\App\Api\Data\AppSearchResultsInterface as IAppSearchResults;

interface AppRepositoryInterface
{
    /**
     * Retrieve app
     *
     * @param mixed $value
     * @param string $field
     * @return IApp
     * @throws NoSuchEntityException
     */
    public function get($value, $field = 'entity_id');

    /**
     * Save app
     *
     * @param IApp $app
     * @return IApp
     */
    public function save(IApp $app);

    /**
     * Delete the app
     *
     * @param IApp $app
     * @return true
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(IApp $app);

    /**
     * Delete by Id
     *
     * @param mixed $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(mixed $id);

    /**
     * Retrieve apps matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return IAppSearchResults
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria): IAppSearchResults;
}
