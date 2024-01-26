<?php
declare(strict_types=1);

namespace SectionBuilder\PurchaseGraphQl\Model\Resolver;

class PlanCancelMutation extends \Xpify\AuthGraphQl\Model\Resolver\AuthSessionAbstractResolver implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $graphQl;

    public function __construct(
        \SectionBuilder\Core\Model\Auth\GraphQl $graphQl
    ) {
        $this->graphQl = $graphQl;
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
        try {
            $merchant = $this->getMerchantSession()->getMerchant();
            $purchaseId = $this->graphQl->getPlanByName($merchant, $args['name'])['id'] ?? null;
            $responseBody = $purchaseId ? $this->graphQl->cancel($merchant, $purchaseId) : [];

            return [
                'message' => 'Thanh cong',
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => 'error'
            ];
        }
    }
}
