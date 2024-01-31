<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model;

use SectionBuilder\Product\Api\Data\SectionInterface;

class SectionRepository implements \SectionBuilder\Product\Api\SectionRepositoryInterface
{
    protected $section;

    protected $sectionFactory;

    protected $collectionFactory;

    protected $collectionProcessor;

    protected $searchResultsFactory;

    public function __construct(
        \SectionBuilder\Product\Model\ResourceModel\Section $section,
        \SectionBuilder\Product\Model\SectionFactory $sectionFactory,
        \SectionBuilder\Product\Model\ResourceModel\Section\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SectionBuilder\Product\Api\Data\SectionSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->section = $section;
        $this->sectionFactory = $sectionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function create()
    {
        return $this->sectionFactory->create();
    }

    public function get(string $field, mixed $value)
    {
        try {
            $section = $this->create();
            $this->section->load($section, $value, $field);

            if (!$section->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Section does not exist.'));
            }
            return $section;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__($e->getMessage()));
        }
    }

    public function save(SectionInterface $section)
    {
        try {
            $this->section->save($section);
            return $section;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        }
    }

    public function delete(SectionInterface $section)
    {
        try {
            $this->section->delete($section);
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($e->getMessage()));
        }
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $sectionCollection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $sectionCollection);
        $searchData = $this->searchResultsFactory->create();
        $searchData->setSearchCriteria($searchCriteria);
        $searchData->setItems($sectionCollection->getItems());
        $searchData->setTotalCount($sectionCollection->getSize());
        return $searchData;
    }
}
