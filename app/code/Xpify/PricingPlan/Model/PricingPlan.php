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
    private array $decodedPrices = [];
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

    /**
     * @inheritDoc
     */
    public function getCode(): ?string
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code): self
    {
        return $this->setData(self::CODE, $code);
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

    public function setDescription(?string $description): self
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get interval price as array
     *
     * array structure:
     * [
     *  [ interval: "ONE_TIME|EVERY_30_DAYS|ANNUAL", amount: 0.00 ]
     * ]
     * @return array
     */
    public function getDataPrices(): array
    {
        if (empty($this->decodedPrices)) {
            try {
                $this->decodedPrices = json_decode($this->getData(self::PRICES), true);
            } catch (\Exception $e) {
                $this->decodedPrices = [];
            }
        }
        return $this->decodedPrices;
    }

    /**
     * Check if interval price is exist
     *
     * @param string $intervalKey
     * @return bool
     */
    public function hasIntervalPrice(string $intervalKey) : bool
    {
        // check if interval is exist in getDataPrices
        return in_array($intervalKey, array_column($this->getDataPrices(), 'interval'));
    }

    /**
     * @inheritDoc
     */
    public function setDataPrice(array $prices) : PricingPlanInterface
    {
        $this->decodedPrices = [];
        return $this->setData(self::PRICES, json_encode($prices));
    }

    /**
     * @inheritDoc
     */
    public function getIntervalPrice(string $key): ?array
    {
        $prices = $this->getDataPrices();
        foreach ($prices as $price) {
            if ($price['interval'] === $key) {
                return $price;
            }
        }
        return null;
    }

    /**
     * Retrive amount by interval
     *
     * @param string $interval
     * @return float
     */
    public function getIntervalAmount(string $interval): float
    {
        return (float) $this->getIntervalPrice($interval)['amount'] ?? 0.0;
    }

    public function getSortOrder(): ?int
    {
        return (int) $this->getData(self::SORT_ORDER);
    }

    public function setSortOrder(mixed $sortOrder): self
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    public function getAppId(): ?int
    {
        return (int) $this->getData(self::APP_ID);
    }

    public function setAppId(mixed $appId): self
    {
        return $this->setData(self::APP_ID, $appId);
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
