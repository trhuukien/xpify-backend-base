<?php
declare(strict_types=1);

namespace SectionBuilder\ProductGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SectionBuilder\Billing\Service\Billing;
use SectionBuilder\Product\Api\Data\SectionInterface as ISection;
use SectionBuilder\Product\Api\SectionRepositoryInterface;

class SectionOwned extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements ResolverInterface
{
    private \SectionBuilder\Billing\Service\Billing $billingService;
    private SectionRepositoryInterface $sectionRepository;

    /**
     * @param Billing $billingService
     * @param SectionRepositoryInterface $sectionRepository
     */
    public function __construct(
        \SectionBuilder\Billing\Service\Billing $billingService,
        SectionRepositoryInterface $sectionRepository
    ) {
        $this->billingService = $billingService;
        $this->sectionRepository = $sectionRepository;
    }

    public function execResolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $section = null;
        if ($value['model']) {
            /** @var ISection $section */
            $section = $value['model'];
        }
        if (empty($section)) {
            $section = $this->sectionRepository->get('entity_id', $value['entity_id']);
        }
        $merchant = $this->getMerchantSession()->getMerchant();
        $price = (float) $section?->getPrice() ?? $value['price'];
        if ($price === 0.0) {
            return true;
        }

        $hasOnetimePayment = $this->billingService->hasPurchasedSection($merchant, $section);
        if ($hasOnetimePayment) {
            return true;
        }

        return $this->billingService->isInActivePlan($merchant, $section);
    }
}
