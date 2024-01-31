<?php
namespace SectionBuilder\Product\Ui\Component\Form\Tag;

use Magento\Framework\Data\OptionSourceInterface;

class OptionTree implements OptionSourceInterface
{
    protected $tagCollectionFactory;

    protected $tagTree;

    public function __construct(
        \SectionBuilder\Tag\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
    ) {
        $this->tagCollectionFactory = $tagCollectionFactory;
    }

    public function toOptionArray()
    {
        return $this->getTagTree();
    }

    protected function getTagTree()
    {
        if ($this->tagTree === null) {
            $tags = [];
            $collection = $this->tagCollectionFactory->create();
            $collection->addFieldToFilter('is_enable', 1);

            foreach ($collection as $tag) {
                $id = $tag->getTagId();
                $tags[$id]['value'] = $id;
                $tags[$id]['label'] = $tag->getName();
            }
            $this->tagTree = $tags;
        }

        return $this->tagTree;
    }
}
