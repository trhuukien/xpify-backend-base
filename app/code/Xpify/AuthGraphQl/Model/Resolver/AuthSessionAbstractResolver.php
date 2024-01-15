<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Shopify\Context;
use Xpify\AuthGraphQl\Model\EnsureMerchantSession;

abstract class AuthSessionAbstractResolver implements ResolverInterface
{
    private $_ensureMerchantSession;

    private function getEnsureMerchantSession(): EnsureMerchantSession
    {
        if (!$this->_ensureMerchantSession) {
            $this->_ensureMerchantSession = \Magento\Framework\App\ObjectManager::getInstance()->get(EnsureMerchantSession::class);
        }
        return $this->_ensureMerchantSession;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->getEnsureMerchantSession()->execute();
        return $this->execResolve($field, $context, $info, $value, $args);
    }

    abstract public function execResolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null);
}