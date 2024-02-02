<?php
namespace SectionBuilder\Core\Model\Config\Source;

class Apps
{
    protected $appRepository;

    protected $criteriaBuilder;

    public function __construct(
        \Xpify\App\Model\AppRepository $appRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->appRepository = $appRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $apps = $this->getApps();
        $result[] = [
            'label' => __('-- Please Select --'),
            'value' => null
        ];
        foreach ($apps as $app) {
            $result[] = [
                'label' => $app->getData('name'),
                'value' => $app->getData('entity_id')
            ];
        }

        return $result;
    }

    public function getApps()
    {
        $searchResults = $this->appRepository->getList($this->criteriaBuilder->create());
        return $searchResults->getItems();
    }
}
