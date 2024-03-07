<?php
declare(strict_types=1);

namespace SectionBuilder\Product\Model;

use SectionBuilder\Product\Api\Data\SectionBuyInterface;

class SectionBuyRepository implements \SectionBuilder\Product\Api\SectionBuyRepositoryInterface
{
    protected $sectionBuy;

    protected $sectionBuyFactory;

    protected $collectionFactory;

    protected $collectionProcessor;
    private \SectionBuilder\Product\Api\Data\PurchasedSectionSearchResultsInterfaceFactory $searchResultsFactory;

    public function __construct(
        \SectionBuilder\Product\Model\ResourceModel\SectionBuy $sectionBuy,
        \SectionBuilder\Product\Model\SectionBuyFactory $sectionBuyFactory,
        \SectionBuilder\Product\Model\ResourceModel\SectionBuy\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SectionBuilder\Product\Api\Data\PurchasedSectionSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->sectionBuy = $sectionBuy;
        $this->sectionBuyFactory = $sectionBuyFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function create(): SectionBuyInterface
    {
        return $this->sectionBuyFactory->create();
    }

    public function get(string $field, int|string $value)
    {
        try {
            $sectionBuy = $this->create();
            $this->sectionBuy->load($sectionBuy, $value, $field);

            if (!$sectionBuy->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Section Buy does not exist.'));
            }
            return $sectionBuy;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__($e->getMessage()));
        }
    }

    public function save(SectionBuyInterface $sectionBuy)
    {
        try {
            $this->sectionBuy->save($sectionBuy);
            return $sectionBuy;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        }
    }

    public function delete(SectionBuyInterface $sectionBuy)
    {
        try {
            $this->sectionBuy->delete($sectionBuy);
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
