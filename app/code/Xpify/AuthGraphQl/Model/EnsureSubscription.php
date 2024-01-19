<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\Merchant\Helper\Subscription;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface as IPricingPlanRepository;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

class EnsureSubscription
{
    private static ?IPricingPlanRepository $pricingPlanRepository = null;

    /**
     * Ensure merchant has valid subscription to access the given resource (codes)
     *
     * @param IMerchant $merchant
     * @param string[] $codes
     * @return void
     * @throws AuthorizationException
     * @throws NoSuchEntityException
     */
    public static function execute(IMerchant $merchant, array $codes): void
    {
        $plans = self::getPricingPlan($codes);
        $subscription = Subscription::getSubscription($merchant);
        if (!in_array($subscription?->getCode(), $codes)) {
            if (count($plans) > 1) {
                $listPlanName = array_map(function (IPricingPlan $plan) {
                    return $plan->getName();
                }, $plans);

                $msg = __("You need upgrade your subscription to one of these plans: %1 to access this resource!", implode(', ', $listPlanName));
            } else {
                $pl = reset($plans);
                $msg = __("You need upgrade your subscription to {$pl->getName()} to access this resource!");
            }
            throw new AuthorizationException($msg);
        }
    }

    /**
     * Get list pricing plan by given list code
     *
     * @param string[] $codes
     * @return IPricingPlan[]
     * @throws NoSuchEntityException
     */
    private static function getPricingPlan(array $codes): array
    {
        $appId = self::getCurrentApp()?->getId();
        if (!$appId) {
            throw new NoSuchEntityException(__("No App defined!"));
        }
        $criteriaBuilder = self::getSearchCriteriaBuilder();
        $criteriaBuilder->addFilter(IPricingPlan::CODE, $codes, 'in');
        $criteriaBuilder->addFilter(IPricingPlan::APP_ID, $appId);
        $searchResults = self::getPricingPlanRepository()->getList($criteriaBuilder->create());
        return $searchResults->getItems();
    }

    /**
     * @return IPricingPlanRepository
     */
    private static function getPricingPlanRepository(): IPricingPlanRepository
    {
        if (!self::$pricingPlanRepository) {
            self::$pricingPlanRepository = \Magento\Framework\App\ObjectManager::getInstance()->get(IPricingPlanRepository::class);
        }
        return self::$pricingPlanRepository;
    }


    /**
     * @return SearchCriteriaBuilder
     */
    private static function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create(SearchCriteriaBuilder::class);
    }


    /**
     * Get context app
     *
     * @return IApp
     * @throws NoSuchEntityException
     */
    private static function getCurrentApp(): IApp
    {
        $app = \Magento\Framework\App\ObjectManager::getInstance()->get(\Xpify\App\Service\GetCurrentApp::class)->get();
        if ($app === null) {
            throw new NoSuchEntityException(__("No App defined!"));
        }
        return $app;
    }
}
