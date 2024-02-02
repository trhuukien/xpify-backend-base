<?php
namespace SectionBuilder\Product\Ui\Component\Form\OptionTree;

use Magento\Framework\Data\OptionSourceInterface;

class Tag extends \SectionBuilder\Core\Model\Ui\Component\Form\OptionTree implements OptionSourceInterface
{
    protected $collectionFactory;

    public function __construct(
        \SectionBuilder\Tag\Model\ResourceModel\Tag\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $filters = [
            ['field' => 'is_enable', 'value' => 1]
        ];

        return $this->getOptionTree(
            $this->collectionFactory->create(),
            $filters
        );
    }
}
