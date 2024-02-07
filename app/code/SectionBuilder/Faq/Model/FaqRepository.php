<?php
declare(strict_types=1);

namespace SectionBuilder\Faq\Model;

use SectionBuilder\Faq\Api\Data\FaqInterface;

class FaqRepository implements \SectionBuilder\Faq\Api\FaqRepositoryInterface
{
    protected $faq;

    protected $faqFactory;

    protected $collectionFactory;

    protected $collectionProcessor;

    protected $searchResultsFactory;

    public function __construct(
        \SectionBuilder\Faq\Model\ResourceModel\Faq $faq,
        \SectionBuilder\Faq\Model\FaqFactory $faqFactory,
        \SectionBuilder\Faq\Model\ResourceModel\Faq\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SectionBuilder\Faq\Api\Data\FaqSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->faq = $faq;
        $this->faqFactory = $faqFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function create()
    {
        return $this->faqFactory->create();
    }

    public function get(string $field, mixed $value)
    {
        try {
            $faq = $this->create();
            $this->faq->load($faq, $value, $field);

            if (!$faq->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Faq does not exist.'));
            }
            return $faq;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__($e->getMessage()));
        }
    }

    public function save(FaqInterface $faq)
    {
        try {
            $this->faq->save($faq);
            return $faq;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        }
    }

    public function delete(FaqInterface $faq)
    {
        try {
            $this->faq->delete($faq);
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($e->getMessage()));
        }
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $faqCollection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $faqCollection);
        $searchData = $this->searchResultsFactory->create();
        $searchData->setSearchCriteria($searchCriteria);
        $searchData->setItems($faqCollection->getItems());
        $searchData->setTotalCount($faqCollection->getSize());
        return $searchData;
    }
}
