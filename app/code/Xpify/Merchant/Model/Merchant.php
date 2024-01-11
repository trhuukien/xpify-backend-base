<?php
declare(strict_types=1);

namespace Xpify\Merchant\Model;

use Magento\Framework\Model\AbstractModel;
use Xpify\Merchant\Api\Data\MerchantInterface as IMerchant;

class Merchant extends AbstractModel implements IMerchant
{
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
}
