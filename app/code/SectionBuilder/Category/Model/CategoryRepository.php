<?php
declare(strict_types=1);

namespace SectionBuilder\Category\Model;

use SectionBuilder\Category\Api\Data\CategoryInterface;

class CategoryRepository implements \SectionBuilder\Category\Api\CategoryRepositoryInterface
{
    protected $category;

    protected $categoryFactory;

    protected $collectionFactory;

    protected $collectionProcessor;

    protected $searchResultsFactory;

    public function __construct(
        \SectionBuilder\Category\Model\ResourceModel\Category $category,
        \SectionBuilder\Category\Model\CategoryFactory $categoryFactory,
        \SectionBuilder\Category\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SectionBuilder\Category\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->category = $category;
        $this->categoryFactory = $categoryFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function create()
    {
        return $this->categoryFactory->create();
    }

    public function get(string $field, mixed $value)
    {
        try {
            $category = $this->create();
            $this->category->load($category, $value, $field);

            if (!$category->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Category does not exist.'));
            }
            return $category;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__($e->getMessage()));
        }
    }

    public function save(CategoryInterface $category)
    {
        try {
            $this->category->save($category);
            return $category;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        }
    }

    public function delete(CategoryInterface $category)
    {
        try {
            $this->category->delete($category);
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($e->getMessage()));
        }
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $categoryCollection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $categoryCollection);
        $searchData = $this->searchResultsFactory->create();
        $searchData->setSearchCriteria($searchCriteria);
        $searchData->setItems($categoryCollection->getItems());
        $searchData->setTotalCount($categoryCollection->getSize());
        return $searchData;
    }
}
