<?php
declare(strict_types=1);

namespace Xpify\PricingPlanGraphQl\Model\Resolver;

use Magento\Framework\Exception\InputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Xpify\PricingPlanGraphQl\Model\PricingPlanFormatter;

class AppPricingPlanQuery extends AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    private PricingPlanRepositoryInterface $pricingPlanRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    private Uid $uidEncoder;

    private PricingPlanFormatter $formatter;

    /**
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param PricingPlanRepositoryInterface $pricingPlanRepository
     * @param Uid $uidEncoder
     * @param PricingPlanFormatter $formatter
     */
    public function __construct(
        SearchCriteriaBuilder $criteriaBuilder,
        PricingPlanRepositoryInterface $pricingPlanRepository,
        Uid $uidEncoder,
        PricingPlanFormatter $formatter
    ) {
        $this->pricingPlanRepository = $pricingPlanRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->uidEncoder = $uidEncoder;
        $this->formatter = $formatter;
    }

    /**
     * @inheritDoc
     */
    public function execResolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $merchant = $this->getEnsureMerchantSession()->getMerchant();
            $id = $this->uidEncoder->decode($args['id']);
            if ($id === null) {
                throw new InputException(__('Invalid ID'));
            }
            $this->criteriaBuilder->addFilter(PricingPlanInterface::APP_ID, $id);
            $searchResult = $this->pricingPlanRepository->getList($this->criteriaBuilder->create());
            $items = [];
            foreach ($searchResult->getItems() as $item) {
                $items[] = $this->formatter->toGraphQlOutput($item);
            }
            return $items;
        } catch (\Exception $e) {
            return [];
        }
    }
}
