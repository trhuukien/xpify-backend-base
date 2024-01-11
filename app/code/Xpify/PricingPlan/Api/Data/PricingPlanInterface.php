<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Api\Data;

use Xpify\App\Api\Data\AppInterface;

interface PricingPlanInterface
{
    const BASE_CURRENCY = AppInterface::CURRENCY_CODE;

    const ID = 'entity_id';
    const STATUS = 'status';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const PRICE = 'price';
    const FREE_TRIAL_DAYS = 'free_trial_days';
    const ENABLE_FREE_TRIAL = 'enable_free_trial';
    const APP_ID = 'app_id';
    const SORT_ORDER = 'sort_order';

    /**
     * Get status
     *
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(mixed $status): self;

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self;

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice(): float;

    /**
     * Set price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice(mixed $price): self;

    /**
     * Get free trial days
     *
     * @return int|null
     */
    public function getFreeTrialDays(): ?int;

    /**
     * Set free trial days
     *
     * @param int $freeTrialDays
     * @return $this
     */
    public function setFreeTrialDays(int $freeTrialDays): self;

    /**
     * Check if free trial is enabled
     *
     * @return bool
     */
    public function isEnableFreeTrial(): bool;

    /**
     * @return int|null
     */
    public function getEnableFreeTrial(): ?int;

    /**
     * Set enable free trial
     *
     * @param int|bool|null $enableFreeTrial
     * @return $this
     */
    public function setEnableFreeTrial(mixed $enableFreeTrial): self;

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder(): ?int;

    /**
     * Set sort order
     *
     * @param int|null $sortOrder
     * @return $this
     */
    public function setSortOrder(mixed $sortOrder): self;

    /**
     * Get app ID
     *
     * @return int|null
     */
    public function getAppId(): ?int;

    /**
     * Set app ID
     *
     * @param string $appId
     * @return $this
     */
    public function setAppId(mixed $appId): self;

    /**
     * Get related app object
     *
     * @return AppInterface|null
     */
    public function getApp(): ?AppInterface;
}
