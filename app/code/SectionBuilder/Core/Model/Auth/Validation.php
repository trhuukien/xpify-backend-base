<?php
declare(strict_types=1);

namespace SectionBuilder\Core\Model\Auth;

class Validation
{
    public function checkAuth($merchant)
    {
        $subscription = \Xpify\Merchant\Helper\Subscription::getSubscription($merchant);

        if ($subscription === null) {
            return false;
        }

        return true;
    }
}
