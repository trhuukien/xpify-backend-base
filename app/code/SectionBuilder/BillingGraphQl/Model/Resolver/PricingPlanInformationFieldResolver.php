<?php
declare(strict_types=1);

namespace SectionBuilder\BillingGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Xpify\PricingPlan\Service\Subscription;

class PricingPlanInformationFieldResolver implements ResolverInterface
{

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $pricingPlan = $value['model'] ?? null;

        $merchant = $context->getExtensionAttributes()->getMerchant();
        if ($pricingPlan?->getId()) {
            return Subscription::getSubscriptionByName($merchant, $pricingPlan->getName());
        }
        return null;
    }
}
