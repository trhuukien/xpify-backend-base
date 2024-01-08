<?php
declare(strict_types=1);

namespace Xpify\PricingPlan\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Xpify\App\Api\AppRepositoryInterface;
use Xpify\App\Api\Data\AppInterface as IApp;
use Xpify\PricingPlan\Api\Data\PricingPlanInterface;

class PricingPlan extends AbstractModel implements PricingPlanInterface
{
    private ?IApp $app = null;

    private AppRepositoryInterface $appRepository;

    /**
     * @param AppRepositoryInterface $appRepository
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Xpify\App\Api\AppRepositoryInterface $appRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->appRepository = $appRepository;
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\PricingPlan::class);
    }

    public function getStatus(): ?int
    {
        return (int) $this->getData(self::STATUS);
    }

    public function setStatus(mixed $status): self
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription(string $description): self
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getPrice(): float
    {
        return (float) $this->getData(self::PRICE);
    }

    public function setPrice(mixed $price): self
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getFreeTrialDays(): ?int
    {
        return (int) $this->getData(self::FREE_TRIAL_DAYS);
    }

    public function setFreeTrialDays(mixed $freeTrialDays): self
    {
        return $this->setData(self::FREE_TRIAL_DAYS, $freeTrialDays);
    }

    public function isEnableFreeTrial(): bool
    {
        return (bool) $this->getEnableFreeTrial();
    }

    public function setEnableFreeTrial(mixed $enableFreeTrial): self
    {
        return $this->setData(self::ENABLE_FREE_TRIAL, $enableFreeTrial);
    }

    public function getAppId(): ?int
    {
        return (int) $this->getData(self::APP_ID);
    }

    public function setAppId(mixed $appId): self
    {
        return $this->setData(self::APP_ID, $appId);
    }

    public function getEnableFreeTrial(): ?int
    {
        return (int) $this->getData(self::ENABLE_FREE_TRIAL);
    }

    /**
     * @inheritDoc
     */
    public function getApp(): ?IApp
    {
        if ($this->app && $this->app->getId()) {
            return $this->app;
        }
        if ($this->getAppId()) {
            try {
                $this->app = $this->appRepository->get($this->getAppId());
                if (!$this->app->getId()) {
                    return null;
                }
            } catch (NoSuchEntityException $e) {
            }
        }

        return null;
    }
}
