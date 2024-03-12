<?php
declare(strict_types=1);

namespace SectionBuilder\Core\Model\Source;

class ProductList implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $sectionRepository;

    protected $criteriaBuilder;

    public function __construct(
        \SectionBuilder\Product\Model\SectionRepository $sectionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        $result = [
            [
                'value' => null,
                'label' => __('-- Please Select --')
            ]
        ];
        $searchCriteria = $this->criteriaBuilder;
        $searchCriteria->addFilter('is_enable', 0);
        $searchCriteria->addFilter('type_id', \SectionBuilder\Product\Model\Config\Source\ProductType::SIMPLE_TYPE_ID);
        $list = $this->sectionRepository->getList($searchCriteria->create());
        foreach ($list->getItems() as $item) {
            $result[] = [
                'value' => $item->getSrc(),
                'label' => $item->getName() . ' | ' . $item->getSrc()
            ];
        }

        return $result;
    }
}
