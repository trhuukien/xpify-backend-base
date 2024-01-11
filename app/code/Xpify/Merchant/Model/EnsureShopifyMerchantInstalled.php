<?php

namespace Xpify\Merchant\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\Merchant\Api\MerchantRepositoryInterface as IMerchantRepository;

class EnsureShopifyMerchantInstalled
{
    private SearchCriteriaBuilder $criteriaBuilder;
    private IMerchantRepository $merchantRepository;

    /**
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param IMerchantRepository $merchantRepository
     */
    public function __construct(
        SearchCriteriaBuilder $criteriaBuilder,
        IMerchantRepository $merchantRepository
    ) {
        $this->criteriaBuilder = $criteriaBuilder;
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * Check if merchant is installed
     *
     * @param int $appId
     * @param string $shop
     * @return bool
     */
    public function execute(int $appId, string $shop): bool
    {
        $this->criteriaBuilder->addFilter(IMerchant::APP_ID, $appId);
        $this->criteriaBuilder->addFilter(IMerchant::SHOP, $shop);
        $this->criteriaBuilder->addFilter(IMerchant::ACCESS_TOKEN, null, 'notnull');
        $this->criteriaBuilder->setPageSize(1);
        $searchCriteria = $this->criteriaBuilder->create();
        $searchResult = $this->merchantRepository->getList($searchCriteria);

        return $searchResult->getTotalCount() > 0;
    }
}
