<?php
declare(strict_types=1);

namespace Xpify\MerchantGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Psr\Log\LoggerInterface;
use Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver;
use Xpify\Core\Helper\Utils;
use Xpify\MerchantGraphQl\Model\SubscriptionFormatter;
use Xpify\PricingPlan\Api\PricingPlanRepositoryInterface as IPricingPlanRepository;
use Xpify\PricingPlan\Model\Source\IntervalType;
use Xpify\Merchant\Api\MerchantSubscriptionRepositoryInterface as ISubscriptionRepository;

class SubscribePricingPlanMutation extends AuthSessionAbstractResolver implements ResolverInterface
{
    private IPricingPlanRepository $pricingPlanRepository;
    private ISubscriptionRepository $subscriptionRepository;
    private \Psr\Log\LoggerInterface $logger;
    private \Xpify\MerchantGraphQl\Model\SubscriptionFormatter $subscriptionFormatter;

    /**
     * @param IPricingPlanRepository $pricingPlanRepository
     * @param ISubscriptionRepository $subscriptionRepository
     * @param LoggerInterface $logger
     * @param SubscriptionFormatter $subscriptionFormatter
     */
    public function __construct(
        IPricingPlanRepository $pricingPlanRepository,
        ISubscriptionRepository $subscriptionRepository,
        \Psr\Log\LoggerInterface $logger,
        \Xpify\MerchantGraphQl\Model\SubscriptionFormatter $subscriptionFormatter
    ) {
        $this->pricingPlanRepository = $pricingPlanRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger = $logger;
        $this->subscriptionFormatter = $subscriptionFormatter;
    }

    public function execResolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $merchant = $this->getMerchantSession()->getMerchant();

        if (!$merchant) {
            throw new GraphQlNoSuchEntityException(__('We can not authorize you! Please try reloading the page. If the problem persists, contact us.'));
        }

        $this->validateArgs($args);
        try {
            $plan = $this->pricingPlanRepository->get($args['input']['plan_id']);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__('Invalid plan ID'));
        }
        $f = $plan->hasIntervalPrice($args['input']['interval']);
        if (!$f) {
            throw new GraphQlNoSuchEntityException(__('Invalid interval'));
        }

        $newSubscription = $this->subscriptionRepository->create();
        $newSubscription->setMerchantId((int) $merchant->getId());
        $newSubscription->setPlanId((int) $plan->getId());
        $newSubscription->setAppId((int) $merchant->getAppId());
        $newSubscription->setCode($plan->getCode());
        $newSubscription->setName($plan->getName());
        $newSubscription->setDescription($plan->getDescription());
        $newSubscription->setPrice($plan->getIntervalAmount($args['input']['interval']));
        $newSubscription->setInterval($args['input']['interval']);
        try {
            $this->subscriptionRepository->save($newSubscription);
        } catch (\Throwable $e) {
            $this->logger->debug($e);
            throw new GraphQlInputException(__("Can't subscribe to this plan! Please try again later."));
        }

        dd($newSubscription);
        return $this->subscriptionFormatter->toGraphQlOutput($newSubscription);
    }

    /**
     * Validate input arguments
     *
     * @param array $args
     * @throws GraphQlInputException|GraphQlNoSuchEntityException
     */
    protected function validateArgs(array &$args): void
    {
        if (!isset($args['input'])) {
            throw new GraphQlNoSuchEntityException(__('Invalid input'));
        }
        $input = &$args['input'];
        $planId = $input['plan_id'] ?? null;
        if (!$planId) {
            throw new GraphQlInputException(__('Invalid plan ID'));
        }
        $input['plan_id'] = (int) Utils::uidToId($planId);
        $interval = $input['interval'] ?? null;
        if (!$interval || !IntervalType::isValidInterval($interval)) {
            throw new GraphQlInputException(__('Invalid interval'));
        }
    }
}
