<?php
declare(strict_types=1);

namespace SectionBuilder\Billing\Service;

use Magento\Framework\Exception\NoSuchEntityException;
use SectionBuilder\Product\Api\SectionBuyRepositoryInterface as IPurchasedSectionRepository;
use Shopify\Context;
use Xpify\App\Service\GetCurrentApp;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use SectionBuilder\Product\Api\Data\SectionInterface as ISection;
use Xpify\Merchant\Exception\ShopifyBillingException;
use Xpify\Merchant\Helper\Subscription;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface as IPricingPlanRepository;

class Billing
{
    private \Xpify\Merchant\Service\Billing $billingService;
    private GetCurrentApp $getCurrentApp;
    private IPurchasedSectionRepository $purchasedSectionRepository;
    private IPricingPlanRepository $pricingPlanRepository;

    /**
     * @param \Xpify\Merchant\Service\Billing $billingService
     * @param GetCurrentApp $getCurrentApp
     * @param IPurchasedSectionRepository $purchasedSectionRepository
     * @param IPricingPlanRepository $pricingPlanRepository
     */
    public function __construct(
        \Xpify\Merchant\Service\Billing $billingService,
        GetCurrentApp $getCurrentApp,
        IPurchasedSectionRepository $purchasedSectionRepository,
        IPricingPlanRepository $pricingPlanRepository
    ) {
        $this->billingService = $billingService;
        $this->getCurrentApp = $getCurrentApp;
        $this->purchasedSectionRepository = $purchasedSectionRepository;
        $this->pricingPlanRepository = $pricingPlanRepository;
    }

    /**
     * Check if the merchant has one time payment, if not, request payment and return the billing url
     *
     * @param IMerchant $merchant
     * @param ISection $section
     * @return string|false
     * @throws NoSuchEntityException|ShopifyBillingException
     */
    public function billingSection(IMerchant $merchant, ISection $section): bool|string
    {
        $config = [
            'chargeName' => $section->getKey(),
            'amount' => $section->getPrice(),
            'currencyCode' => \Xpify\App\Api\Data\AppInterface::CURRENCY_CODE,
            'interval' => \Xpify\PricingPlan\Model\Source\IntervalType::INTERVAL_ONE_TIME,
        ];

        $app = $this->getCurrentApp->get();
        if (!$app?->getRemoteId()) {
            throw new NoSuchEntityException(__('App not found'));
        }
        $sign = \Xpify\Core\Helper\Utils::createHmac([
            'data' => ['_mid' => $merchant->getId(), '_rid' => $app->getRemoteId()],
            'buildQuery' => true,
            'buildQueryWithJoin' => true,
        ], \Xpify\Core\Model\Constants::SYS_SECRET_KEY);
        $config['return_url'] =
            Context::$HOST_SCHEME . '://' .
            Context::$HOST_NAME .
            '/section-builder/checkout/success' .
            "/_rid/{$this->getCurrentApp->get()?->getRemoteId()}" .
            "/_mid/{$merchant->getId()}" .
            "/_sign/$sign";
        if (!$this->hasOneTimePayment($merchant, $section)) {
            [$billingUrl] = $this->billingService->requestPayment($merchant, $config);
            return $billingUrl;
        }
        return false;
    }

    /**
     * Check if the section is in active plan of the merchant
     *
     * @param IMerchant $merchant
     * @param ISection $section
     * @return bool
     */
    public function isInActivePlan(IMerchant $merchant, ISection $section): bool
    {
        if (((bool) $section->getPlanId()) === false) {
            return false;
        }
        $merchantSubscription = Subscription::getSubscription($merchant);
        if ($merchantSubscription->getPlanId() . "" !== $section->getPlanId() . "") {
            return false;
        }
        // Ensure the current subscription is active in Shopify
        return $this->billingService->hasActivePayment($merchant, [
            'chargeName' => $merchantSubscription->getName(),
            'interval' => $merchantSubscription->getInterval(),
        ]);
    }

    public function hasPurchasedSection(IMerchant $merchant, ISection $section): bool
    {
        $searchCriteria = \Magento\Framework\App\ObjectManager::getInstance()->create(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria->addFilter('merchant_shop', $merchant->getShop());
        $searchCriteria->addFilter('product_id', $section->getId());
        $searchCriteria->setPageSize(1);
        $purchasedSectionResults = $this->purchasedSectionRepository->getList($searchCriteria->create());
        if ($purchasedSectionResults->getTotalCount() > 0) {
            $purchasedSection = current($purchasedSectionResults->getItems());
            $details = $purchasedSection->getDetails();
            if ($details) {
                $decodedDetails = json_decode($details, true);
                if ($decodedDetails['status'] === 'ACTIVE') {
                    return true;
                }
            }
        }

        // Ensure the merchant has an active one time payment
        return $this->hasOneTimePayment($merchant, $section);
    }

    /**
     * Check if the merchant has one time payment
     *
     * @param IMerchant $merchant
     * @param ISection $section
     * @return bool
     */
    protected function hasOneTimePayment(IMerchant $merchant, ISection $section): bool
    {
        return $this->billingService->hasActivePayment($merchant, [
            'chargeName' => $section->getKey(),
            'interval' => \Xpify\PricingPlan\Model\Source\IntervalType::INTERVAL_ONE_TIME,
        ]);
    }
}
