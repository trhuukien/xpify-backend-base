<?php
declare(strict_types=1);

namespace Xpify\App\Model;

use Magento\Framework\Model\AbstractModel;
use Xpify\App\Api\Data\AppInterface as IApp;

class App extends AbstractModel implements IApp
{
    protected function _construct()
    {
        $this->_init(\Xpify\App\Model\ResourceModel\App::class);
    }

    /**
     * @inheritDoc
     */
    public function getRemoteId(): ?string
    {
        return $this->getData(self::REMOTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRemoteId(int|string $remoteId): IApp
    {
        return $this->setData(self::REMOTE_ID, $remoteId);
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): IApp
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getApiKey(): ?string
    {
        return $this->getData(self::API_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setApiKey(string $apiKey): IApp
    {
        return $this->setData(self::API_KEY, $apiKey);
    }

    /**
     * @inheritDoc
     */
    public function getSecretKey(): ?string
    {
        return $this->getData(self::SECRET_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setSecretKey(string $secretKey): IApp
    {
        return $this->setData(self::SECRET_KEY, $secretKey);
    }

    /**
     * @inheritDoc
     */
    public function getScopes(): ?string
    {
        return $this->getData(self::SCOPES);
    }

    /**
     * @inheritDoc
     */
    public function setScopes(string $scopes): IApp
    {
        return $this->setData(self::SCOPES, $scopes);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): IApp
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt): IApp
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getBillingRequired(): ?int
    {
        return (int) $this->getData(self::BILLING_REQUIRED);
    }

    /**
     * @inheritDoc
     */
    public function isBillingRequired(): bool
    {
        return (bool) $this->getBillingRequired();
    }

    public function getApiVersion(): ?string
    {
        return $this->getData(self::API_VERSION);
    }

    public function setApiVersion(?string $version): IApp
    {
        return $this->setData(self::API_VERSION, $version);
    }
}
