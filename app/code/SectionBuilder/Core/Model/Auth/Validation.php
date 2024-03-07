<?php
declare(strict_types=1);

namespace SectionBuilder\Core\Model\Auth;

class Validation
{
    protected $graphQl;

    protected $billing;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\GraphQl $graphQl,
        \Xpify\Merchant\Service\Billing $billing
    ) {
        $this->graphQl = $graphQl;
        $this->billing = $billing;
    }

    public function hasOneTime($merchant, $oneTimePurchaseKey)
    {
        return $this->graphQl->hasOneTimePayment($merchant, $oneTimePurchaseKey);
    }

    public function hasPlan($merchant, $planName)
    {
        return $this->graphQl->hasSubscription($merchant, $planName);
    }

    public function redirectPagePurchase($merchant, $config): void
    {
        if (!$this->billing->hasActivePayment($merchant, $config)) {
            [$billingUrl] = $this->billing->requestPayment($merchant, $config);

            /* Redirect to payment shopify app */
            throw new \Xpify\AuthGraphQl\Exception\GraphQlShopifyReauthorizeRequiredException(
                __("Payment required."),
                null,
                0,
                true,
                $billingUrl
            );
        }
    }
}
