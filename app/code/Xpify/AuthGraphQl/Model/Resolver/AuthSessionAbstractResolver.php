<?php
declare(strict_types=1);

namespace Xpify\AuthGraphQl\Model\Resolver;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Xpify\AuthGraphQl\Model\EnsureMerchantSession;
use Xpify\AuthGraphQl\Model\EnsureSubscription;
use Xpify\Core\Model\Constants;

/**
 * Class Abstract authentication resolver
 *
 * @since 1.0.0
 */
abstract class AuthSessionAbstractResolver implements ResolverInterface
{
    private ?EnsureMerchantSession $_ensureMerchantSession = null;

    /**
     * Get authenticated merchant session
     *
     * @return EnsureMerchantSession
     * @deprecated 1.0.1 use context->getExtensionAttributes()->getMerchant() instead
     */
    protected function getMerchantSession(): EnsureMerchantSession
    {
        return $this->getEnsureMerchantSession();
    }

    /**
     * Determine if the current request requires a subscription
     * Return value must be a pricing plan code related to the context app
     * If null is returned, no subscription is required
     *
     * @return string[]
     * @since 1.0.0
     */
    protected function pricingPlansRequired(): array
    {
        return [];
    }

    /**
     * Ensure the current merchant has a valid subscription to access the current resource
     *
     * @return void
     * @throws AuthorizationException|\Magento\Framework\Exception\NoSuchEntityException
     * @since 1.0.0
     */
    protected function ensureSubscription(): void
    {
        $requiredPlans = $this->pricingPlansRequired();
        if (!empty($requiredPlans)) {
            EnsureSubscription::execute($this->getMerchantSession()->getMerchant(), $requiredPlans);
        }
    }

    /**
     * @inheritDoc
     * @since 1.0.0
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $this->ensureSubscription();
        } catch (AuthorizationException $e) {
            throw new GraphQlAuthorizationException(__($e->getMessage()), $e);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (\Throwable $e) {
            self::getLogger()->debug($e);
            throw new GraphQlNoSuchEntityException(__(Constants::INTERNAL_SYSTEM_ERROR_MESS), $e instanceof \Exception ? $e : null);
        }
        return $this->execResolve($field, $context, $info, $value, $args);
    }

    /**
     * Execute the resolver
     *
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     * @since 1.0.0
     */
    abstract public function execResolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null);

    /**
     * @deprecated use getMerchantSession() instead
     * @return EnsureMerchantSession
     * @since 1.0.0
     * @deprecated 1.0.1
     */
    protected function getEnsureMerchantSession(): EnsureMerchantSession
    {
        if (!$this->_ensureMerchantSession) {
            $this->_ensureMerchantSession = \Magento\Framework\App\ObjectManager::getInstance()->get(EnsureMerchantSession::class);
        }
        return $this->_ensureMerchantSession;
    }

    private static function getLogger(): \Psr\Log\LoggerInterface
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
    }
}
