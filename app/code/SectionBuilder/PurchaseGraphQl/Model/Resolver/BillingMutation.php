<?php
declare(strict_types=1);

namespace SectionBuilder\PurchaseGraphQl\Model\Resolver;

class BillingMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $authValidation;

    protected $sectionRepository;

    protected $criteriaBuilder;
    protected $pricingPlanRepository;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\SectionRepository $sectionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Xpify\PricingPlan\Api\PricingPlanRepositoryInterface $pricingPlanRepository
    ) {
        $this->authValidation = $authValidation;
        $this->sectionRepository = $sectionRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->pricingPlanRepository = $pricingPlanRepository;
    }

    /**
     * @inheirtdoc
     */
    public function execResolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $merchant = $this->getMerchantSession()->getMerchant();

        if ($args['is_plan']) {
            $appId = $this->getMerchantSession()->getMerchant()->getAppId();
            $criteriaBuilder = $this->criteriaBuilder;
            $criteriaBuilder->addFilter(\Xpify\PricingPlan\Api\Data\PricingPlanInterface::NAME, $args['name']);
            $criteriaBuilder->addFilter(\Xpify\PricingPlan\Api\Data\PricingPlanInterface::STATUS, 1);
            $criteriaBuilder->addFilter(\Xpify\PricingPlan\Api\Data\PricingPlanInterface::APP_ID, $appId);
            $searchResults = $this->pricingPlanRepository->getList($criteriaBuilder->create());
            foreach ($searchResults->getItems() as $pricingPlan) {
                $price = $pricingPlan->getIntervalPrice($args['interval'])['amount'] ?? null;
                break;
            }
        } else {
            $sectionRepository = $this->sectionRepository->get(
                \SectionBuilder\Product\Api\Data\SectionInterface::NAME,
                $args['name']
            );
            $price = $sectionRepository->getPrice();
        }

        if (empty($price)) {
            return [
                'message' => 'Da xay ra loi',
                'status' => 'warning'
            ];
        }

        $config = [
            'chargeName' => $args['name'],
            'amount' => $price,
            'currencyCode' => \Xpify\App\Api\Data\AppInterface::CURRENCY_CODE,
            'interval' => $args['interval'],
        ];

        $this->authValidation->redirectPagePurchase($merchant, $config);

        return [
            'message' => 'Da purchase roi',
            'status' => 'warning'
        ];
    }
}
