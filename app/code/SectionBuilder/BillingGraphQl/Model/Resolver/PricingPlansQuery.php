<?php
declare(strict_types=1);

namespace SectionBuilder\BillingGraphQl\Model\Resolver;

class PricingPlansQuery extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $auth;

    protected $collectionFactory;

    protected $currentApp;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\GraphQl $auth,
        \Xpify\PricingPlan\Model\ResourceModel\PricingPlan\CollectionFactory $collectionFactory,
        \Xpify\App\Service\GetCurrentApp $currentApp
    ) {
        $this->auth = $auth;
        $this->collectionFactory = $collectionFactory;
        $this->currentApp = $currentApp;
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
        $appId = $this->currentApp->get()->getId();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('app_id', $appId);
        $collection->addFieldToFilter('status', 1);
        $result = $collection->getData();

        $plans = [];
        foreach ($result as $key => $plan) {
            $plans[$key]['plan'] = $plan;
            $plans[$key]['plan']['id'] = $plan['entity_id'];
            $plans[$key]['plan']['prices'] = $plan['prices'] ? json_decode($plan['prices'], true) : [];
            $plans[$key]['plan']['currency'] = \Xpify\App\Api\Data\AppInterface::CURRENCY_CODE;

            $plans[$key]['information'] = $this->auth->getPlanByName($merchant, $plan['code']);
        }

        return $plans;
    }
}
