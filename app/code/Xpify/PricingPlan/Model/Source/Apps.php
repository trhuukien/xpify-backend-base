<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Model\Source;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Xpify\App\Api\AppRepositoryInterface as IAppRepository;

class Apps implements OptionSourceInterface
{
    private IAppRepository $appRepository;
    private SearchCriteriaBuilder $criteriaBuilder;

    /**
     * @param IAppRepository $appRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     */
    public function __construct(
        IAppRepository $appRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->appRepository = $appRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $searchResults = $this->appRepository->getList($this->criteriaBuilder->create());
        $options = [];
        foreach ($searchResults->getItems() as $app) {
            $options[] = [
                'label' => $app->getName(),
                'value' => $app->getId(),
            ];
        }
        return $options;
    }
}
