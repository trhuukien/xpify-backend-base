<?php
declare(strict_types=1);

namespace SectionBuilder\BillingGraphQl\Model\Resolver;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SectionBuilder\Billing\Service\Billing;
use SectionBuilder\Core\Model\Auth\Validation;
use SectionBuilder\Product\Api\Data\SectionInterface as ISection;
use SectionBuilder\Product\Model\SectionRepository;
use Xpify\App\Service\GetCurrentApp;
use Xpify\AuthGraphQl\Exception\GraphQlShopifyReauthorizeRequiredException;
use Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver;
use Xpify\Core\Model\Logger;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface;

class PurchaseSectionMutation extends AuthSessionAbstractResolver implements ResolverInterface
{
    private $authValidation;
    private $sectionRepository;
    private $criteriaBuilder;
    private $pricingPlanRepository;
    private Uid $uidEncoder;
    private GetCurrentApp $getCurrentApp;
    private Billing $sectionBilling;

    /**
     * @param Validation $authValidation
     * @param SectionRepository $sectionRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param PricingPlanRepositoryInterface $pricingPlanRepository
     * @param Uid $uidEncoder
     * @param GetCurrentApp $getCurrentApp
     * @param Billing $sectionBilling
     */
    public function __construct(
        \SectionBuilder\Core\Model\Auth\Validation $authValidation,
        \SectionBuilder\Product\Model\SectionRepository $sectionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Xpify\PricingPlan\Api\PricingPlanRepositoryInterface $pricingPlanRepository,
        Uid $uidEncoder,
        GetCurrentApp $getCurrentApp,
        Billing $sectionBilling
    ) {
        $this->authValidation = $authValidation;
        $this->sectionRepository = $sectionRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->pricingPlanRepository = $pricingPlanRepository;
        $this->uidEncoder = $uidEncoder;
        $this->getCurrentApp = $getCurrentApp;
        $this->sectionBilling = $sectionBilling;
    }

    /**
     * @throws NoSuchEntityException
     * @throws GraphQlInputException
     * @throws GraphQlShopifyReauthorizeRequiredException
     */
    public function execResolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $merchant = $this->getMerchantSession()->getMerchant();
        $section = $this->sectionRepository->get(ISection::ID, $args['id']);
        if (!$section->getId()) {
            throw new GraphQlInputException(__("Section not found!"));
        }
        if (!empty($section->getPrice())) {
            try {
                $billingUrl = $this->sectionBilling->billingSection($merchant, $section);
            } catch (\Exception $error) {
                Logger::getLogger('section_builder_section_purchase.log')?->debug(__("%1. %2", $error->getMessage(), $error->getTraceAsString())->render());
                throw new GraphQlNoSuchEntityException(__("Can not process your purchase at the moment. Please try again later."));
            }
            if ($billingUrl !== false) {
                throw new \Xpify\AuthGraphQl\Exception\GraphQlShopifyReauthorizeRequiredException(
                    __("Payment required."),
                    null,
                    0,
                    true,
                    $billingUrl
                );
            }
        }
        return \SectionBuilder\ProductGraphQl\Model\SectionFormatter::toGraphQl($section);
    }
}
