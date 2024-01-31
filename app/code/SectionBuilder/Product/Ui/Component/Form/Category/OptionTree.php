<?php
namespace SectionBuilder\Product\Ui\Component\Form\Category;

use Magento\Framework\Data\OptionSourceInterface;

class OptionTree implements OptionSourceInterface
{
    protected $categoryCollectionFactory;

    protected $categoryTree;

    public function __construct(
        \SectionBuilder\Category\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function toOptionArray()
    {
        return $this->getCategoryTree();
    }

    protected function getCategoryTree()
    {
        if ($this->categoryTree === null) {
            $categories = [];
            $collection = $this->categoryCollectionFactory->create();
            $collection->addFieldToFilter('is_enable', 1);

            foreach ($collection as $category) {
                $id = $category->getCategoryId();
                $categories[$id]['value'] = $id;
                $categories[$id]['label'] = $category->getName();
            }
            $this->categoryTree = $categories;
        }

        return $this->categoryTree;
    }
}
