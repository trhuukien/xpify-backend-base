<?php
namespace SectionBuilder\Product\Ui\Component\Form\OptionTree;

use Magento\Framework\Data\OptionSourceInterface;

class PricingPlan extends \SectionBuilder\Core\Model\Ui\Component\Form\OptionTree implements OptionSourceInterface
{
    protected $collectionFactory;

    public function __construct(
        \Xpify\PricingPlan\Model\ResourceModel\PricingPlan\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $filters = [
            ['field' => 'status', 'value' => 1]
        ];

        return $this->getOptionTree(
            $this->collectionFactory->create(),
            $filters,
            [
                ['label' => ' ', 'value' => null]
            ]
        );
    }
}
