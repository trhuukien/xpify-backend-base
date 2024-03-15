<?php
declare(strict_types=1);

namespace Xpify\Merchant\Api\Data;

use Magento\Framework\Exception\NoSuchEntityException;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface as IPricingPlan;

interface MerchantSubscriptionInterface
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DEACTIVATED = 'DEACTIVATED';
    const ID = 'entity_id';
    const MERCHANT_ID = 'merchant_id';
    const PLAN_ID = 'plan_id';
    const APP_ID = 'app_id';
    const CODE = 'code';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const PRICE = 'price';
    const INTERVAL = 'interval';
    const CREATED_AT = 'created_at';
    const STATUS = 'status';
    const SUBSCRIPTION_ID = 'subscription_id';

    /**
     * Get transaction status
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Get subscription id
     *
     * @return string|null
     */
    public function getSubscriptionId(): ?string;

    /**
     * Set transaction status
     *
     * @param string $status
     * @return MerchantSubscriptionInterface
     */
    public function setStatus(string $status): self;

    /**
     * Set subscription id
     *
     * @param string $subscriptionId
     * @return MerchantSubscriptionInterface
     */
    public function setSubscriptionId(string $subscriptionId): self;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param mixed $value
     * @return MerchantSubscriptionInterface
     */
    public function setId(mixed $value): self;

    /**
     * @return int|null
     */
    public function getMerchantId(): ?int;

    /**
     * @param int $merchantId
     * @return MerchantSubscriptionInterface
     */
    public function setMerchantId(int $merchantId): self;

    /**
     * @return int|null
     */
    public function getPlanId(): ?int;

    /**
     * @param int $planId
     * @return MerchantSubscriptionInterface
     */
    public function setPlanId(int $planId): self;

    /**
     * @return int|null
     */
    public function getAppId(): ?int;

    /**
     * @param int $appId
     * @return MerchantSubscriptionInterface
     */
    public function setAppId(int $appId): self;

    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @param string $code
     * @return MerchantSubscriptionInterface
     */
    public function setCode(string $code): self;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return MerchantSubscriptionInterface
     */
    public function setName(string $name): self;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     * @return MerchantSubscriptionInterface
     */
    public function setDescription(?string $description): self;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @param float $price
     * @return MerchantSubscriptionInterface
     */
    public function setPrice(float $price): self;

    /**
     * @return string
     */
    public function getInterval(): string;

    /**
     * @param string $interval
     * @return MerchantSubscriptionInterface
     */
    public function setInterval(string $interval): self;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return MerchantSubscriptionInterface
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * @return IMerchant|null
     * @throws NoSuchEntityException
     */
    public function getMerchant(): ?IMerchant;

    /**
     * Related pricing plan or readonly pricing plan (when current plan id not existed in database anymore)
     * @return IPricingPlan|null
     */
    public function getPlanPricing(): ?IPricingPlan;
}
