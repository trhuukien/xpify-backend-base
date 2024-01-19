<?php
declare(strict_types=1);

namespace Xpify\Merchant\Helper;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Xpify\Merchant\Api\Data\MerchantSubscriptionInterface as ISubscription;
use Xpify\Merchant\Api\MerchantSubscriptionRepositoryInterface as ISubscriptionRepository;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

final class Subscription
{
    private static array $subscriptions = [];
    private static ?ISubscriptionRepository $subscriptionRepository = null;

    /**
     * Check if merchant has subscription
     *
     * @param IMerchant $merchant
     * @return array [bool, ISubscription|null]
     */
    public static function hasSubscription(IMerchant $merchant): array
    {
        $subscription = self::getSubscription($merchant);
        return [
            (bool) $subscription?->getId(),
            $subscription
        ];
    }

    /**
     * Get existed subscription of the given merchant
     *
     * @param IMerchant $merchant
     * @return ISubscription|null
     */
    public static function getSubscription(IMerchant $merchant): ?ISubscription
    {
        if (!isset(self::$subscriptions[$merchant->getId()])) {
            $criteriaBuilder = self::getSearchCriteriaBuilder();
            $criteriaBuilder->addFilter(ISubscription::MERCHANT_ID, $merchant->getId());
            $criteriaBuilder->addFilter(ISubscription::APP_ID, $merchant->getAppId());
            $searchResults = self::getSubscriptionRepository()->getList($criteriaBuilder->create());
            if ($searchResults->getTotalCount() > 0) {
                $subs = $searchResults->getItems();
                self::$subscriptions[$merchant->getId()] = reset($subs);
            }
        }
        return self::$subscriptions[$merchant->getId()] ?? null;
    }

    /**
     * @return ISubscriptionRepository
     */
    private static function getSubscriptionRepository(): ISubscriptionRepository
    {
        if (!self::$subscriptionRepository) {
            self::$subscriptionRepository = \Magento\Framework\App\ObjectManager::getInstance()->get(ISubscriptionRepository::class);
        }
        return self::$subscriptionRepository;
    }

    /**
     * @return SearchCriteriaBuilder
     */
    private static function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create(SearchCriteriaBuilder::class);
    }
}
