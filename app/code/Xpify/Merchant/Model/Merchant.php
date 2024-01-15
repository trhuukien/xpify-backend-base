<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model;

use Magento\Framework\Model\AbstractModel;
use Shopify\Clients\Graphql;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

class Merchant extends AbstractModel implements IMerchant
{
    private ?Graphql $graphQlClient = null;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Merchant::class);
    }

    /**
     * @inheritDoc
     */
    public function getSessionId(): ?string
    {
        return (string) $this->getData(IMerchant::SESSION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSessionId(?string $sessionId): IMerchant
    {
        return $this->setData(IMerchant::SESSION_ID, $sessionId);
    }

    /**
     * @inheritDoc
     */
    public function getIsOnline(): int
    {
        return (int) $this->getData(IMerchant::IS_ONLINE);
    }

    /**
     * @inheritDoc
     */
    public function setIsOnline(?int $isOnline): IMerchant
    {
        return $this->setData(IMerchant::IS_ONLINE, $isOnline);
    }

    /**
     * @inheritDoc
     */
    public function getAppId(): ?int
    {
        return (int) $this->getData(IMerchant::APP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAppId(?int $appId): IMerchant
    {
        return $this->setData(IMerchant::APP_ID, $appId);
    }

    /**
     * @inheritDoc
     */
    public function getShop(): ?string
    {
        return (string) $this->getData(IMerchant::SHOP);
    }

    /**
     * @inheritDoc
     */
    public function setShop(?string $shop): IMerchant
    {
        return $this->setData(IMerchant::SHOP, $shop);
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken(): ?string
    {
        return (string) $this->getData(IMerchant::ACCESS_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken(?string $accessToken): IMerchant
    {
        return $this->setData(IMerchant::ACCESS_TOKEN, $accessToken);
    }

    /**
     * @inheritDoc
     */
    public function getUserId(): ?int
    {
        return (int) $this->getData(IMerchant::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId(?int $userId): IMerchant
    {
        return $this->setData(IMerchant::USER_ID, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getUserFirstName(): ?string
    {
        return (string) $this->getData(IMerchant::USER_FIRST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setUserFirstName(?string $userFirstName): IMerchant
    {
        return $this->setData(IMerchant::USER_FIRST_NAME, $userFirstName);
    }

    /**
     * @inheritDoc
     */
    public function getUserLastName(): ?string
    {
        return (string) $this->getData(IMerchant::USER_LAST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setUserLastName(?string $userLastName): IMerchant
    {
        return $this->setData(IMerchant::USER_LAST_NAME, $userLastName);
    }

    /**
     * @inheritDoc
     */
    public function getUserEmail(): ?string
    {
        return (string) $this->getData(IMerchant::USER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setUserEmail(?string $userEmail): IMerchant
    {
        return $this->setData(IMerchant::USER_EMAIL, $userEmail);
    }

    /**
     * @inheritDoc
     */
    public function getUserEmailVerified(): ?int
    {
        return (int) $this->getData(IMerchant::USER_EMAIL_VERIFIED);
    }

    /**
     * @inheritDoc
     */
    public function setUserEmailVerified(?int $userEmailVerified): IMerchant
    {
        return $this->setData(IMerchant::USER_EMAIL_VERIFIED, $userEmailVerified);
    }

    /**
     * @inheritDoc
     */
    public function getAccountOwner(): ?int
    {
        return (int) $this->getData(IMerchant::ACCOUNT_OWNER);
    }

    /**
     * @inheritDoc
     */
    public function setAccountOwner(?int $accountOwner): IMerchant
    {
        return $this->setData(IMerchant::ACCOUNT_OWNER, $accountOwner);
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): ?string
    {
        return (string) $this->getData(IMerchant::LOCALE);
    }

    /**
     * @inheritDoc
     */
    public function setLocale(?string $locale): IMerchant
    {
        return $this->setData(IMerchant::LOCALE, $locale);
    }

    /**
     * @inheritDoc
     */
    public function getCollaborator(): ?int
    {
        return (int) $this->getData(IMerchant::COLLABORATOR);
    }

    /**
     * @inheritDoc
     */
    public function setCollaborator(?int $collaborator): IMerchant
    {
        return $this->setData(IMerchant::COLLABORATOR, $collaborator);
    }

    public function getCreatedAt(): ?string
    {
        return (string) $this->getData(IMerchant::CREATED_AT);
    }

    public function setCreatedAt(?string $createdAt): IMerchant
    {
        return $this->setData(IMerchant::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): ?string
    {
        return (string) $this->getData(IMerchant::UPDATED_AT);
    }

    public function setUpdatedAt(?string $updatedAt): IMerchant
    {
        return $this->setData(IMerchant::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getExpiresAt(): ?string
    {
        return (string) $this->getData(IMerchant::EXPIRES_AT);
    }

    /**
     * @inheritDoc
     */
    public function setExpiresAt(?string $expiresAt): IMerchant
    {
        return $this->setData(IMerchant::EXPIRES_AT, $expiresAt);
    }

    public function getState(): ?string
    {
        return (string) $this->getData(IMerchant::STATE);
    }

    public function setState(?string $state): IMerchant
    {
        return $this->setData(IMerchant::STATE, $state);
    }

    /**
     * @inheritDoc
     */
    public function getScope(): ?string
    {
        return (string) $this->getData(IMerchant::SCOPE);
    }

    /**
     * @inheritDoc
     */
    public function setScope(?string $scope): IMerchant
    {
        return $this->setData(IMerchant::SCOPE, $scope);
    }

    /**
     * Get the Graphql client for merchant
     *
     * @return Graphql|null
     * @throws \Shopify\Exception\MissingArgumentException
     */
    public function getGraphqlClient(): ?Graphql
    {
        if (!$this->graphQlClient) {
            if (!$this->getShop() || !$this->getAccessToken()) {
                return null;
            }

            $this->graphQlClient = new GraphQl($this->getShop(), $this->getAccessToken());
        }

        return $this->graphQlClient;
    }
}
