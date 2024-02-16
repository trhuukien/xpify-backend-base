<?php
namespace SectionBuilder\Product\Ui\Component\Form\OptionTree;

use Magento\Framework\Data\OptionSourceInterface;

class PricingPlan extends \SectionBuilder\Core\Model\Ui\Component\Form\OptionTree implements OptionSourceInterface
{
    protected $collectionFactory;

    protected $config;

    public function __construct(
        \Xpify\PricingPlan\Model\ResourceModel\PricingPlan\CollectionFactory $collectionFactory,
        \SectionBuilder\Core\Model\Config $config
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
    }

    public function toOptionArray()
    {
        $filters = [
            ['field' => 'status', 'value' => 1],
            ['field' => 'app_id', 'value' => $this->config->getAppConnectingId()]
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
