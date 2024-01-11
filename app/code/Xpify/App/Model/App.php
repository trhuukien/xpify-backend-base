<?php
declare(strict_types=1);

namespace Xpify\App\Model;

use Magento\Framework\Model\AbstractModel;
use Xpify\App\Api\Data\AppInterface;

class App extends AbstractModel implements AppInterface
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
    public function setRemoteId(int|string $remoteId): AppInterface
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
    public function setName(string $name): AppInterface
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
    public function setApiKey(string $apiKey): AppInterface
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
    public function setSecretKey(string $secretKey): AppInterface
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
    public function setScopes(string $scopes): AppInterface
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
    public function setCreatedAt(string $createdAt): AppInterface
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
    public function setUpdatedAt(string $updatedAt): AppInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
