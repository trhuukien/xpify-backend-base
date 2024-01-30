<?php
declare(strict_types=1);

namespace SectionBuilder\Tag\Model;

use SectionBuilder\Tag\Api\Data\TagInterface;

class TagRepository implements \SectionBuilder\Tag\Api\TagRepositoryInterface
{
    protected $tag;

    protected $tagFactory;

    protected $collectionFactory;

    protected $collectionProcessor;

    protected $searchResultsFactory;

    public function __construct(
        \SectionBuilder\Tag\Model\ResourceModel\Tag $tag,
        \SectionBuilder\Tag\Model\TagFactory $tagFactory,
        \SectionBuilder\Tag\Model\ResourceModel\Tag\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SectionBuilder\Tag\Api\Data\TagSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->tag = $tag;
        $this->tagFactory = $tagFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function create()
    {
        return $this->tagFactory->create();
    }

    public function get(string $field, mixed $value)
    {
        try {
            $tag = $this->create();
            $this->tag->load($tag, $value, $field);

            if (!$tag->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Tag does not exist.'));
            }
            return $tag;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__($e->getMessage()));
        }
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $tagCollection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $tagCollection);
        $searchData = $this->searchResultsFactory->create();
        $searchData->setSearchCriteria($searchCriteria);
        $searchData->setItems($tagCollection->getItems());
        $searchData->setTotalCount($tagCollection->getSize());
        return $searchData;
    }

    public function save(TagInterface $tag)
    {
        try {
            $this->tag->save($tag);
            return $tag;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        }
    }

    public function delete(TagInterface $tag)
    {
        try {
            $this->tag->delete($tag);
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($e->getMessage()));
        }
    }
}
